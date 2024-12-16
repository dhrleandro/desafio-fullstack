#!/bin/sh

cd /app

if [ -e /app/.env ];
then
    echo "ENV already exists"
else
    cp /app/.env.example /app/.env

    # VITE_API_URL localhost
    sed -i 's/VITE_API_URL=/VITE_API_URL=http:\/\/localhost:8000\/api/g' /app/.env
fi

npm run dev -- --host 0.0.0.0
