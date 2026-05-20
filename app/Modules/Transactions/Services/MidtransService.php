<?php

namespace App\Modules\Transactions\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;

class MidtransService{
    /**
     * Memaksa pengaturan config tepat sebelum request dieksekusi
     */
    private function setupConfig() {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key'); // Ditambahkan untuk kelengkapan SDK
        Config::$isProduction  = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Validasi Hard-Stop untuk membantu debugging
        if (empty(Config::$serverKey)) {
            Log::channel('midtrans')->error('[MIDTRANS CONFIG ERROR] Server Key Kosong! Pastikan config/midtrans.php terbaca.');
            throw new \Exception('Midtrans Server Key is empty. Configuration failed.');
        }
    }

    public function createSnapToken(array $params): string{
        try{
            $this->setupConfig(); // Panggil setup config di sini
            return Snap::getSnapToken($params);
        }catch(\Exception $e){
            Log::error('Failed to create snap token'. $e->getMessage());
            throw $e;
        }
    }

    public function handleNotification(): array{
        try{
            $this->setupConfig(); // Panggil setup config di sini
            $notification = new Notification();
            return [
                "order_id" => $notification->order_id,
                "transaction_status" => $notification->transaction_status,
                "gross_amount" => $notification->gross_amount,
                "custom_field1" => $notification->custom_field1,
                "custom_field2" => $notification->custom_field2,

            ];
        }catch(\Exception $e){
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            throw $e;
        }
    }

}