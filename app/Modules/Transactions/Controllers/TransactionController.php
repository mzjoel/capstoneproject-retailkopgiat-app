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

        try{
            $transaction = $this->transactionService->createTransaction($request->all());
            
            $paymentUrl = null;
            // if ($transaction->payment_method === 'qris') {
            //     $paymentUrl = "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . bin2hex(random_bytes(8));
            // }

            if ($request->header('X-Inertia')) {
                return redirect()->route('transaction', ['id' => $transaction->id]);
            }

            return response()->json([
                'result' => ['status' => 'Success 201', 'message' => 'Order created'],
                'data' => [
                    'transaction_id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'payment_method' => $transaction->payment_method,
                    'grand_total' => $transaction->grand_total,
                    'items' => $transaction->items,
                    
                    // 'payment_url' => $paymentUrl
                ]
            ], 201);
        }catch(Exception $e){
            \Log::error('Checkout error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'result' => [
                    'status' => 'Error 500', 
                    'message' => 'Internal Server Error: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine()
                ]
            ], 500);
        }
    }

    public function getTransactionStatus($id){
        $transaction = Transaction::with('details.product.category')->findOrFail($id);
        return response()->json(['result' => ['status' => 'Success 200'], 'data' => $transaction]);
    }


    public function updateStatus(Request $request, $id){
       try {
            $transaction = $this->transactionService->updateTransactionStatus($id, $request->status);
            return response()->json(['result' => ['status' => 'Success 200', 'message' => 'Status updated']]);
        } catch (\Exception $e) {
            return response()->json(['result' => ['status' => 'Error 400', 'message' => $e->getMessage()]], 400);
        }
    }

    // public function handleNotification(Request $request){
    //     $orderId = $request->order_id;
    //     $transactionStatus = $request->transaction_status;
    //     $transaction = Transaction::where('order_id', $orderId)->first();
    //     if ($transaction) {
    //         if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
    //             $transaction->update(['status' => 'paid']);
    //         } else if ($transactionStatus == 'expire' || $transactionStatus == 'cancel') {
    //             $transaction->update(['status' => 'cancelled']);
    //         }
    //     }
    //     return response()->json(['status' => 'OK']);
    // }
}