#!/bin/bash


APP_DIR="/var/www/capstoneproject-retailkopgiat-app"
LOG_FILE="/home/ubuntu/deployment_full.log"

# Fungsi Logging Universal
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log_message "Strating Deployment..."

if [ -d "$APP_DIR" ]; then
    cd "$APP_DIR" || exit 1
    
    
    log_message "🔐 Mengatur Permissions (ubuntu:www-data)..."
    # Mengembalikan kepemilikan agar Git bisa melakukan pull tanpa error permission
    sudo chown -R ubuntu:www-data .
    sudo chmod -R 775 storage bootstrap/cache
    git config --global --add safe.directory "$APP_DIR"

    log_message "📥 Syncing GitHub (branch: production)..."
    # Reset paksa untuk membuang perubahan manual di server dan sinkron dengan origin
    git fetch origin production && git reset --hard origin/production
    
    log_message "📦 Build Backend (Composer)..."
    composer install --no-dev --optimize-autoloader --quiet

    log_message "📦 Build Frontend (NPM)..."
    npm install --quiet && npm run build --quiet

    log_message "⚙️ Optimasi Laravel (Clear Cache & Migrate)..."
    php artisan optimize:clear --no-interaction
    php artisan migrate --force --no-interaction
    
    log_message "✅ Deployment Source Code Selesai."
else
    log_message "❌ GAGAL: Folder aplikasi ($APP_DIR) tidak ditemukan!"
    exit 1
fi

log_message "Testing Intergating ML-ENGINE"
php artisan ml:check --no-ansi > /tmp/ml_check.log 2>&1

if[ $? -eq 0]; then
    log_message "SUCCESS: Integration ML-ENGINE Has Been Completed: \n$(cat /tmp/ml_check.log >> "$LOG_FILE")"
else 
    log_message "FAIL : Integration ML-ENGINE Has Been Failed: \n$(cat /tmp/ml_check.log >> "$LOG_FILE")"
    exit 1
fi

log_message "Change Permission for storage and bootstrap/cache directory"
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
log_message "CI/CD Deployemnet Has Been Completed"










