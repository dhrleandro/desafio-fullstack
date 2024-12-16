#!/bin/sh

cd /app

if [ -e /app/.env ];
then
    echo "ENV already exists"
else
    cp /app/.env.example /app/.env

    # replace DB_HOST 127.0.0.1 with postgresql service (docker-compose.yml)
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=postgresql/g' /app/.env
fi

composer install

php artisan cache:clear
php artisan config:cache
php artisan key:generate
php artisan migrate
php artisan db:seed

php artisan serve --host=0.0.0.0 --port=8000
