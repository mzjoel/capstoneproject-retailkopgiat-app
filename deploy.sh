#!/bin/bash

# --- KONFIGURASI PATH ---
APP_DIR="/var/www/capstoneproject-retailkopgiat-app"
ML_DIR="/home/ubuntu/ml-engine"
PYTHON_BIN="/home/ubuntu/miniconda3/envs/ml_env/bin/python"
LOG_FILE="/home/ubuntu/deployment_full.log"

# Fungsi Logging Universal
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log_message "🚀 MEMULAI DEPLOYMENT GLOBAL (APP & ML ENGINE)..."

# ==========================================
# BAGIAN 1: LARAVEL APP DEPLOYMENT
# ==========================================
log_message "--- 1/2: Memulai Deployment Laravel App ---"
cd "$APP_DIR" || { log_message "❌ GAGAL: Direktori App tidak ditemukan"; exit 1; }

log_message "🔐 Mengatur Permissions & Git Safe Directory..."
sudo chown -R ubuntu:www-data .
sudo chmod -R 775 storage bootstrap/cache
git config --global --add safe.directory "$APP_DIR"

log_message "📥 Syncing dengan GitHub (Branch: production)..."
git fetch origin production && git reset --hard origin/production
git clean -fd

log_message "📦 Menginstal Dependencies (Composer & NPM)..."
composer install --no-dev --optimize-autoloader --no-interaction --quiet
npm install --quiet && npm run build --quiet

log_message "⚙️ Optimasi Laravel (Clear, Migrate, Seed, Cache)..."
php artisan optimize:clear --no-interaction
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction

# Perintah cache digabung dalam satu tahap agar efisien
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

log_message "🔒 Mengunci kembali Permissions storage..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
log_message "✅ Bagian Laravel Selesai."


# ==========================================
# BAGIAN 2: ML ENGINE DEPLOYMENT
# ==========================================
log_message "--- 2/2: Memulai Deployment ML Engine ---"
cd "$ML_DIR" || { log_message "❌ GAGAL: Direktori ML tidak ditemukan"; exit 1; }

log_message "📥 Pulling perubahan terbaru untuk ML Engine..."
git fetch origin production && git reset --hard origin/production

log_message "📦 Sync Dependencies & Update Library di ml_env..."
# Menjalankan pip upgrade dan install secara berurutan
$PYTHON_BIN -m pip install --upgrade pip --quiet
$PYTHON_BIN -m pip install -r requirements.txt --no-cache-dir --quiet

log_message "📸 Membuat Snapshot Library untuk Audit..."
$PYTHON_BIN -m pip freeze > last_success_requirements.txt

log_message "🔄 Merestart Service FASTAPI..."
sudo systemctl daemon-reload
sudo systemctl restart fastapi

# Menunggu sebentar untuk inisialisasi aplikasi
sleep 3
if sudo systemctl is-active --quiet fastapi; then
    log_message "✅ ML Engine BERHASIL RUNNING!"
else 
    log_message "❌ GAGAL: ML Engine tidak berjalan. Periksa log sistem."
fi

log_message "=========================================================="
log_message "✅ SELURUH PROSES DEPLOYMENT SELESAI PADA $(date)"
log_message "=========================================================="