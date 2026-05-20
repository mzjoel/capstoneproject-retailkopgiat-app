<?php

namespace App\Modules\Transactions\Controllers;


use App\Http\Controllers\Controller; 
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Models\TransactionDetail;
use App\Modules\Transactions\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Exception;

class TransactionController extends Controller{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function validationPaymentDetails(Request $request){
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'result' => ['status' => 'Error 422', 'message' => $validator->errors()->first()]
            ], 422);
        }

        try{
           $data = $this->transactionService->validateTransaction($request->items);
            return response()->json([
                'result' => ['status' => 'Success 200', 'message' => 'Summary prepared'],
                'data' => array_merge($data, [
                    'available_payment_methods' => [
                        ['code' => 'qris', 'name' => 'QRIS'],
                        ['code' => 'cash', 'name' => 'Tunai']
                    ]
                ])
            ]);
        }catch(Exception $e){
            return response()->json(['result' => ['status' => 'Error 400', 'message' => $e->getMessage()]], 400);
        }
    }

    
    public function checkout(Request $request)
    {
        try{

            $validator = Validator::make($request->all(), [
            'customer_profile_id' => 'required|exists:customer_profiles,id',
            'payment_method' => 'required|in:qris,cash',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => ['status' => 'Error 422', 'message' => $validator->errors()->first()]], 422);
            }

            $transaction = $this->transactionService->createTransaction($request->all());
            
            $responseData = [
                "transaction_id" => $transaction->id,
                "order_id"       => $transaction->order_id,
                "grand_total"    => $transaction->grand_total,
                "status"         => $transaction->status,
            ];

            if($request->payment_method==='qris'){
                $responseData['midtrans'] = 'pending';
                $responseData['snap_token'] = $transaction->snap_token;
            }
            return response()->json([
                'result' => ['status' => 'Success 201', 'message' => 'Order created'],
                'data' => $responseData,
            ], 201);
        }catch(\Throwable $e){
            Log::channel('midtrans')->error('[CHECKOUT FAILED] Internal/Midtrans Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'result' => ['status' => 'Error 500', 'message' => $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine()]
            ], 500);
        }
    }

    public function midtransCallback(Request $request){
        Log::channel('midtrans')->info('Payload Dari midtrans', $request->all());
        try{
            $transaction = $this->transactionService->handleMidtransNotification();
            Log::channel('midtrans')->info('[WEBHOOK SUCCESS] DB Terupdate', [
                'order_id' => $transaction ? $transaction->order_id : 'N/A',
                'new_status' => $transaction ? $transaction->status : 'N/A'
            ]);
            return response()->json([
                'status' => 'Success 200',
                'message' => 'Notification received and processsed'
            ]);
        }catch(\Exception $e){
            // LOG 3: Gagal update DB (misal order_id tidak ditemukan)
            Log::channel('midtrans')->error('[WEBHOOK FAILED] Gagal memproses data', [
                'message' => $e->getMessage(),
                'payload' => $request->all()
            ]);
            // Log::error('Webhook Error: '. $e->getMessage());
            return response()->json(['error' => 'Failed to process'], 500);
        }
    }

    public function getTransactionStatus(Request $request, $id){
        try {
            $transaction = $this->transactionService->getTransactionStatus($id, $request->user());

            if ($request->wantsJson()) {
                return response()->json([
                    'result' => ['status' => 'Success 200'],
                    'data' => $transaction
                ]);
            }

            return Inertia::render('Transaction/OrderStatus', [
                'id' => $id,
                'rawTransaction' => $transaction
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Transaction/OrderStatus', [
                'rawTransaction' => null,
                'errorMessage' => $e->getMessage()
            ]);
        }
    }


    public function updateStatus(Request $request, $id){
       try {
            $transaction = $this->transactionService->updateTransactionStatus($id, $request->status);
            return response()->json(['result' => ['status' => 'Success 200', 'message' => 'Status updated']]);
        } catch (\Exception $e) {
            return response()->json(['result' => ['status' => 'Error 400', 'message' => $e->getMessage()]], 400);
        }
    }

    public function History(Request $request){
         try {
            $user = $request->user();
            
            // Mengambil data mentah dari Service (Relasi DB)
            $transactions = $this->transactionService->getTransactionHistory($user);

            if ($request->wantsJson()) {
                return response()->json([
                    'result' => ['status' => 'Success 200'],
                    'data' => $transactions
                ]);
            }

            // Merender halaman Inertia Vue dan passing data sebagai Props
            return Inertia::render('Transaction/TransactionHistory', [
                'rawTransactions' => $transactions
            ]);

        } catch (\Exception $e) {
            // Fallback jika terjadi error (misal profil belum lengkap)
            return Inertia::render('Transaction/TransactionHistory', [
                'rawTransactions' => [],
                'errorMessage' => $e->getMessage()
            ]);
        }
    }


}
