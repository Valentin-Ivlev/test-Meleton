#!/bin/bash

cp .env.example .env

composer install

php artisan key:generate

php artisan config:clear

echo "Setup completed successfully!"
