#!/bin/sh

cd /app

echo "-------------------------"
echo "Running tests"
echo "-------------------------"

php artisan test
sleep 1

echo "-------------------------"
echo "Restoring database"
echo "-------------------------"

php artisan migrate
php artisan db:seed

echo "-------------------------"