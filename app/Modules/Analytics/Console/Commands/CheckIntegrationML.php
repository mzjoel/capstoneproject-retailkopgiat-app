<?php

namespace App\Modules\Analytics\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckIntegrationML extends Command {
    protected $signature = 'ml:check';
    protected $description = 'Tracking Flow Integration Between Apps With ML Engine';

    public function handle()
    {
        $this->info(str_repeat('=', 50));
         $this->info("MEMULAI PELACAKAN INTEGRASI ML ENGINE");
         $this->info(str_repeat('=', 50));

        $logMessage = "\n[ " . now()->format('Y-m-d H:i:s') . " ] RUNNING ML INTEGRATION CHECK\n";
        $mlUrl = env('ML_ENGINE_URL', 'http://127.0.0.1:5000');

        //Check Status ML Engine
        $this->warn("\n[FLOW 1] Mengecek Status ML Engine ({$mlUrl})...");
        try{
            $response = Http::timeout(5)->get($mlUrl . '/openapi.json'); 
             if ($response->successful() || $response->status() == 404) {
                $this->info(" ML Engine: FastAPI Active");
                $logMessage .= "1. ML Engine Running: SUCCESS\n";
             }else{
                throw new Exception("HTTP Status: " . $response->status());
             }
        }catch(Exception $e){
            $this->error("   ❌ ML Engine: MATI / TIDAK MERESPON");
            $this->line("      Detail: " . $e->getMessage());
            $logMessage .= "1. ML Engine Running: FAILED (" . $e->getMessage() . ")\n";
             $this->saveLog($logMessage);
            return Command::FAILURE; 
        }

        $this->warn("\n[FLOW 2] Mengecek Ketersediaan Database Lokal/Produksi...");
        try{
            DB::connection()->getPdo();
             $dbName = DB::connection()->getDatabaseName();
            $totalInteractions = DB::table('user_interactions')->count();
             $this->info("   ✅ Database ({$dbName}): TERHUBUNG");
            $this->line("   ℹ️  Total Data Log Perilaku saat ini: {$totalInteractions} baris");
            $logMessage .= "2. Database Connection: SUCCESS (Log count: {$totalInteractions})\n";
        }catch(Exception $e){
            $this->error("   ❌ Database: GAGAL TERHUBUNG");
             $this->line("      Detail: " . $e->getMessage());
             $logMessage .= "2. Database Connection: FAILED (" . $e->getMessage() . ")\n";
              $this->saveLog($logMessage);
              return Command::FAILURE;
        }

        $this->warn("\n[FLOW 3 & 4] Menguji Kinerja API Rekomendasi ML...");
        try{
            $sampleUserId = DB::table('customer_profiles')->value('user_id') ?? 1;
            $this->line("   Mencoba request prediksi untuk User ID: {$sampleUserId}");
            $endpoint = $mlUrl . '/recommend/' . $sampleUserId; 
            $mlResponse = Http::timeout(15)->get($endpoint);
            if($mlResponse->successful()){
                $data = $mlResponse->json();
                if(isset($data['status']) && $data['status'] === 'success'){
                     $this->info("   ✅ API Rekomendasi: MENGHASILKAN DATA (Working)");
                     $weather = $data['context']['weather'] ?? 'Unknown';
                     $prefs = implode(', ', $data['context']['user_prefs'] ?? ['None']);
                     $totalRecs = count($data['recommendations'] ?? []);
                      $this->line("   ☁️  Cuaca Terdeteksi ML  : {$weather}");
                    $this->line("   👤 Preferensi User DB : {$prefs}");
                    $this->line("   📄 Total Rekomendasi  : {$totalRecs} item didapatkan.");
                    $logMessage .= "3 & 4. Recommendation API Working: SUCCESS (Returned {$totalRecs} items)\n";
                }else{
                     throw new Exception("ML Engine membalas namun dengan format JSON tak terduga: " . json_encode($data));
                }
            }else{
                throw new Exception("ML API Code: " . $mlResponse->status() . " - " . $mlResponse->body());
            }
        }catch(Exception $e){
            $this->error("   ❌ API Rekomendasi: GAGAL MEMPROSES / ERROR");
            $this->line("      Detail: " . $e->getMessage());
            $this->line("      ⚠️ Catatan: Cek apakah Python berhasil connect ke Database dan SVD tidak error.");
            $logMessage .= "3 & 4. Recommendation API Working: FAILED (" . $e->getMessage() . ")\n";
            $this->saveLog($logMessage);
            return Command::FAILURE;
        }
    }

    private function saveLog($message){
        $logPath = storage_path('logs/ml_tracker.log');
         file_put_contents($logPath, $message, FILE_APPEND);
          $this->line("Log detail disimpan di: storage/logs/ml_tracker.log");
    }
}