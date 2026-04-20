<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class TransactionController extends Controller{

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
            $calculation = $this->calculateOrderSummary($request->items);
            return response()->json([
                'result' => [
                    'status' => 'Success 200', 
                    'message' => 'Payment details prepared. Please choose payment method.'
                ],
                'data' => [
                    'items_breakdown' => $calculation['items'],
                    'grand_total' => $calculation['total'],
                    'available_payment_methods' => [
                        ['code' => 'qris', 'name' => 'QRIS (Midtrans)', 'icon' => 'qr_code'],
                        ['code' => 'cash', 'name' => 'Tunai di Kasir', 'icon' => 'payments']
                    ]
                ]
            ], 200);
        }catch(Exception $e){
            return response()->json(['result' => ['status' => 'Error 400', 'message' => $e->getMessage()]], 400);
        }
    }

    private function calculateOrderSummary($items){
        foreach($items as $item){
            $product = Product::find($item['product_id']);
            if (!$product) {
                throw new Exception("Produk dengan ID {$item['product_id']} tidak ditemukan.");
            }
            $subTotal = $product->price * $item['quantity'];
            $currentTotal += $subTotal;
            $validatedItems[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => $item['quantity'],
                'sub_total' => (float) $subTotal
            ];
        }
        return [
            'items' => $validatedItems,
            'total' => (float) $currentTotal
        ];
    }
    public function checkout(Request $request){
        $validator = Validator::make($request->all(), [
            'customer_profile_id' => 'required|exists:customer_profiles,id',
            'payment_method' => 'required|in:qris,cash',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => ['status' => 'Error 422', 'message' => $validator->errors()->first()]], 422);
        }

        try{
            return DB::transaction(function () use ($request){
                 $calculation = $this->calculateOrderSummary($request->items);
                 $transaction = Transaction::create([
                    'customer_profile_id' => $request->customer_profile_id,
                    'grand_total' => $calculation['total'],
                    'payment_method' => $request->payment_method,
                    'status' => 'pending', 
                    'order_id' => 'GIAT-' . time() . '-' . $request->customer_profile_id 
                ]);

                foreach ($orderDetails as $detail) {
                    $transaction->details()->create($detail);
                }

                // $paymentUrl = null;
                // if ($request->payment_method === 'qris') {
                //     // Di sini nantinya Anda memanggil Midtrans SDK
                //     // $paymentUrl = Midtrans::getSnapUrl($transaction);
                //     $paymentUrl = "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . bin2hex(random_bytes(10));
                // }

                 return response()->json([
                    'result' => ['status' => 'Success 201', 'message' => 'Order created successfully'],
                    'data' => [
                        'transaction_id' => $transaction->id,
                        'order_id' => $transaction->order_id,
                        'grand_total' => $grandTotal,
                        'status' => $transaction->status,
                        'payment_url' => $paymentUrl
                    ]
                ], 201);
            });
        }catch(Exception $e){
            return response()->json(['result' => ['status' => 'Error 500', 'message' => $e->getMessage()]], 500);
        }
    }

    public function getTransactionStatus($id){
        $transaction = Transaction::with(['details.product', 'customerProfile'])->findOrFail($id);

        return response()->json([
            'result' => ['status' => 'Success 200', 'message' => 'Transaction status retrieved'],
            'data' => $transaction
        ], 200);
    }


    public function updateStatus(Request $request, $id){
        $transaction = Transaction::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:paid,preparing,ready_for_pickup,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => ['status' => 'Error 422', 'message' => $validator->errors()->first()]], 422);
        }

        $transaction->update(['status' => $request->status]);

        return response()->json([
            'result' => ['status' => 'Success 200', 'message' => "Order status updated to {$request->status}"],
            'data' => $transaction
        ], 200);

    }

    public function handleNotification(Request $request){
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $transaction = Transaction::where('order_id', $orderId)->first();
        if ($transaction) {
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                $transaction->update(['status' => 'paid']);
            } else if ($transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $transaction->update(['status' => 'cancelled']);
            }
        }
        return response()->json(['status' => 'OK']);
    }
}