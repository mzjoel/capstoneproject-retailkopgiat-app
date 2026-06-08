#!/bin/bash

APP_DIR="/var/www/capstoneproject-retailkopgiat-app"
LOG_FILE="/home/ubuntu/deployment_full.log"

log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log_message "Strating Deployment..."

if [ -d "$APP_DIR" ]; then
    cd "$APP_DIR" || exit 1
    
    log_message "🔐 Setting Permissions (ubuntu:www-data)..."
    sudo chown -R ubuntu:www-data .
    sudo chmod -R 775 storage bootstrap/cache
    git config --global --add safe.directory "$APP_DIR"

    log_message "📥 Syncing GitHub (branch: production)..."
    git fetch origin production && git reset --hard origin/production
    
    log_message "📦 Build Backend (Composer)..."
    composer install --no-dev --optimize-autoloader --quiet

    log_message "📦 Build Frontend (NPM)..."
    npm install --quiet && npm run build --quiet

    log_message "⚙️ Optimasi Laravel (Clear Cache & Migrate)..."
    php artisan storage:link
    php artisan optimize:clear --no-interaction
    php artisan migrate --force --no-interaction
    php artisan db:seed --class=AdminSeeder --force --no-interaction
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    log_message "✅ Source Code Deployment Completed."
else
    log_message "❌ Aplikasi Folder ($APP_DIR) tidak ditemukan!"
    exit 1
fi

log_message "Testing Intergating ML-ENGINE"
php artisan ml:check --no-ansi > /tmp/ml_check.log 2>&1

if [ $? -eq 0 ]; then
    log_message "✅ INTEGRASI SUKSES: Laravel dapat berkomunikasi dengan ML Engine secara sempurna."
    cat /tmp/ml_check_result.log >> "$LOG_FILE"
else
    log_message "⚠️ PERINGATAN: Integrasi ML Gagal atau Server ML tidak merespons (Status 500/Timeout)."
    log_message "🔍 Detail Error:"
    cat /tmp/ml_check_result.log | grep -i "error" | tee -a "$LOG_FILE"
    log_message "💡 Catatan: Aplikasi tetap live, namun fitur Rekomendasi akan menggunakan mode Fallback."
fi

log_message "Change Permission for storage and bootstrap/cache directory"
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
log_message "CI/CD Deployemnet Has Been Completed"










