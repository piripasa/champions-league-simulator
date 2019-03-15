#!/usr/bin/env bash

set -e
sudo docker-compose down
echo "Starting services"
sudo docker-compose up -d
#sudo docker-compose exec php cp /var/www/html/.env.example /var/www/html/.env
echo "Installing dependencies"
sudo docker-compose exec php composer install
sudo docker-compose exec php php -r "file_exists('.env') || copy('.env.example', '.env');"
sudo docker-compose exec php php artisan key:generate
sudo docker-compose exec php chgrp -R www-data storage bootstrap/cache
sudo docker-compose exec php chmod -R ug+rwx storage bootstrap/cache
echo "Migrating database"
rm -f bootstrap/cache/*.php
sudo docker-compose exec php php artisan migrate --seed
echo "Database migrated & seeded"
#echo "Unit testing..."
#sudo docker-compose exec php vendor/bin/phpunit

echo "Server started at http://127.0.0.1"