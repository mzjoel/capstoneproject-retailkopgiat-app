<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Models\TransactionDetail;
use App\Modules\Analytics\Models\CustomerProfile;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Services\CatalogService;
use App\Modules\Transactions\Services\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class TransactionService{
    protected $catalogService, $midtransService;

    public function __construct(CatalogService $catalogService, MidtransService $midtransService){
        $this->catalogService = $catalogService;
        $this->midtransService = $midtransService;
    }

    public function validateTransaction(array $items){
        $total = 0;
        $validatedItems = [];

        foreach($items as $item){
            $product = $this->catalogService->getProductForCheckout($item['product_id']);
            if (!$product) {
                throw new Exception("Produk dengan ID {$item['product_id']} tidak tersedia atau tidak ditemukan.");
            }
            $subTotal = $product->price * $item['quantity'];
            $total += $subTotal;
            $validatedItems[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => $item['quantity'],
                'sub_total' => (float) $subTotal,
            ];
        }

        return [
            'items' => $validatedItems,
            'total' => (float) $total
        ];
    }


    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $validated = $this->validateTransaction($data['items']);
            $orderId = 'GIAT-' . time() . '-' . $data['customer_profile_id'];

            $transaction = Transaction::create([
                'customer_profile_id' => $data['customer_profile_id'],
                'order_id'            => $orderId,
                'grand_total'         => $validated['total'],
                'payment_method'      => $data['payment_method'],
                'status'              => 'pending'
            ]);

            $itemDetailsForMidtrans = [];

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $transaction->details()->create([
                    'product_id'           => $item['product_id'],
                    'quantity'             => $item['quantity'],
                    'price_transaction' => $item['price'],
                ]);
                $itemDetailsForMidtrans[] = [
                    'id' => $item['product_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => substr($product->name, 0, 50),
                ];
            }
            $snapToken = null;
            if($data['payment_method'] === 'qris'){
                $customerProfile = DB::table('customer_profiles')
                                     ->where('id', $data['customer_profile_id'])
                                     ->first();
                $params = [
                    'transaction_details' => [
                        'order_id'     => $orderId,
                        'gross_amount' => $validated['total'],
                    ],
                    'customer_details' => [
                        'first_name' => $customerProfile->name,
                        'email'      => auth()->user()->email ?? 'customer@koperasi-giat.com',
                    ],
                    'item_details' => $itemDetailsForMidtrans,
                    // 'enabled_payments' => ['qris'],
                    'custom_field1' => $orderId,
                    'custom_field2' => $data['customer_profile_id'],
                ];

                $snapToken = $this->midtransService->createSnapToken($params);
            }
            $transaction->setAttribute('items', $validated['items']);
            $transaction->setAttribute('snap_token', $snapToken);
            return $transaction;
        });
    }

    public function handleMidtransNotification(){
        $notification = $this->midtransService->handleNotification();
        $orderId = $notification['order_id'];
        $transactionStatus = $notification['transaction_status'];
        $fraudStatus = $notification['fraud_status'] ?? null;

        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) {
            throw new \Exception("Transaction with order_id {$orderId} not found.");
        }
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'challenge') {
                $transaction->status = 'pending'; // Butuh review manual
            } else {
                $transaction->status = 'paid'; // Atau 'completed' sesuai schema Anda
            }
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $transaction->status = 'cancelled';
        } else if ($transactionStatus == 'pending') {
            $transaction->status = 'pending';
        }
        $transaction->save();
        return $transaction;
    }

    public function updateTransactionStatus($orderId, $newStatus){
        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) {
            throw new Exception("Transaksi dengan Order ID {$orderId} tidak ditemukan.");
        }
        if ($transaction->status === 'completed' && $newStatus === 'cancelled') {
            throw new Exception("Pesanan yang sudah selesai tidak dapat dibatalkan.");
        }
        $transaction->update(['status' => $newStatus]);
        return $transaction;
    }

    
    public function getTransactionHistory($user){
        if(!$user->customerProfile){
            throw new Exception("Profil customer tidak ditemukan.");
        }
        $customer = $user->customerProfile;
        $transactions = Transaction::where('customer_profile_id', $customer->id)
            ->with(['details.product'])
            ->latest()
            ->get();
        return $transactions;
    }

    public function getTransactionStatus($id, $user)
    {
        // PENTING: Validasi agar User A tidak bisa melihat pesanan User B
        if (!$user->customerProfile) {
            throw new Exception("Profil customer tidak ditemukan.");
        }

        // Ambil transaksi beserta relasi detail, produk, dan kategorinya
        return Transaction::with('details.product.category')
            ->where('id', $id)
            ->where('customer_profile_id', $user->customerProfile->id)
            ->firstOrFail();
    }

    

}