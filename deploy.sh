#!/bin/bash


APP_DIR="/var/www/capstoneproject-retailkopgiat-app"
LOG_FILE="/home/ubuntu/deployment_full.log"

# Fungsi Logging Universal
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log_message "Strating Deployment..."

if [ -d "$APP_DIR"]; then
    log_message "App Directory is found in $APP_DIR"
    cd "$APP_DIR" || exit 1
    sudo chown -R ubuntu:www-data .
    sudo chmod -R 775 storage bootstrap/cache
    git config --global --add safe.directory "$APP_DIR"
    log_message "Syncronize Repository"
    git fetch origin production && git reset --hard origin/production
    git clean -fd
    log_message "Make Sure Updated Dependencies"
    composer install --no-dev --optimize-autoloader --no-interaction --quiet
    npm install --quiet && npm run build --quiet
    log_message "Optimize Laravel"
    php artisan optimize:clear --no-interaction
    php artisan migrate --force --no-interaction
    php artisan migrate db:seed --force --no-interaction 
    log_message "Clear Cache Laravel"
    php artisan optimize:clear --no-interaction
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
    php artisan view:cache --no-interaction
    log_message "Deployment App Has Been Completed"
else 
    log_message "FAIL : Directory App Not Found, Please Check Directory On Your Server Is Existing..."
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










