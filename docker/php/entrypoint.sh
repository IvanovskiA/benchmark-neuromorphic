#!/bin/bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --force
fi

if [ ! -d node_modules ]; then
    npm install
fi

if [ ! -d public/build ]; then
    npm run build
fi

php artisan migrate --force
php artisan db:seed --force

rm -f /var/www/html/public/hot

if grep -q '^APP_URL=' .env; then
    sed -i 's|^APP_URL=.*|APP_URL=http://localhost:8080|' .env
else
    echo 'APP_URL=http://localhost:8080' >> .env
fi

php artisan config:clear
php artisan view:clear

if [ ! -f /data/cicids.csv ]; then
    DATASET_OUTPUT=/data /usr/local/bin/python3 /var/www/html/python/datasets/generate_samples.py || true
fi

php-fpm -D
nginx -g 'daemon off;'
