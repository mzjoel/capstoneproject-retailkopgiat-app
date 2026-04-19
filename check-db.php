<?php
// check_db_v2.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Memastikan environment terbaca
$host = config('database.connections.mysql.host');
$port = config('database.connections.mysql.port');

echo "=== DEBUG INFO ===\n";
echo "Host terbaca: '" . ($host ?? 'NULL') . "'\n";
echo "Port terbaca: '" . ($port ?? 'NULL') . "'\n";

if (empty($host)) {
    die("❌ Error: Host database tidak ditemukan! Pastikan .env sudah benar dan tidak ada cache (jalankan php artisan config:clear).\n");
}

echo "\n--- Mengetes Jaringan ke $host:$port ---\n";
// ... (lanjutkan dengan kode socket sebelumnya)