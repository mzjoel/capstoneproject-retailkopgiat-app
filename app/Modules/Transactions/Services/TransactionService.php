<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Models\TransactionDetail;
use App\Modules\Catalog\Services\CatalogService;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService{
    protected $catalogService;

    public function __construct(CatalogService $catalogService){
        $this->catalogService = $catalogService;
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

            $validated = $this->validateTransaction($data['items']);

            $transaction = Transaction::create([
                'customer_profile_id' => $data['customer_profile_id'],
                'order_id'            => 'GIAT-' . time() . '-' . $data['customer_profile_id'],
                'grand_total'         => $validated['total'],
                'payment_method'      => $data['payment_method'],
                'status'              => 'pending'
            ]);

            // 3. Simpan Detail Transaksi ke Database
            foreach ($validated['items'] as $item) {
                $transaction->details()->create([
                    'product_id'           => $item['product_id'],
                    'quantity'             => $item['quantity'],
                    'price_transaction' => $item['price'],
                ]);
            }
            $transaction->setAttribute('items', $validated['items']);

            return $transaction;
        });
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