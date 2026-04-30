echo "🚀 Memulai Deployment Produksi..."

cd /var/www/capstoneproject-retailkopgiat-app || exit

echo "🔐 Fixing Permissions..."
sudo chown -R ubuntu:www-data .
sudo chmod -R 775 storage bootstrap/cache
git config --global --add safe.directory /var/www/capstoneproject-retailkopgiat-app


echo "📥 Syncing with GitHub..."
git fetch origin production
git reset --hard origin/production
git clean -fd


echo "📦 Installing Composer Dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "📦 Building Frontend Assets..."
npm install
npm run build

echo "⚙️ Optimizing Laravel..."
php artisan optimize:clear --no-interaction
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache


echo "✅ Deployment Selesai di $(date)!"