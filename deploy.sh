#!/bin/bash

set -e

echo "Starting Deployment - $(date)"

cd /var/www/capstoneproject-retailkopgiat-app

git fetch origin production
git reset --hard origin/production
git clean -fd

composer install --no-dev --prefer-dist --optimize-autoloader
npm install
npm run build 

php artisan config:cache
php artisan migrate --force
php artisan db:seed --class=ProductSeeder
php artisan optimize:clear

echo "cleaning cache--"
php artisan optimize:clear

echo "Deployment finished - $(date)"
