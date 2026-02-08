#!/bin/bash

# Configuration
PROJECT_DIR="/var/www/olmos-talent"
GIT_REPO="origin"
BRANCH="main"

echo "--- Inizio Deploy ---"

# 1. Pull dei cambiamenti
cd $PROJECT_DIR || exit
echo "Pulling latest changes from $BRANCH..."
git pull $GIT_REPO $BRANCH

# 2. Reset dei permessi
echo "Setting permissions..."
sudo chown -R www-data:www-data $PROJECT_DIR
sudo chmod -R 775 $PROJECT_DIR/public/assets

# 3. Pulizia Cache (opzionale, se presente un sistema di caching)
# echo "Clearing cache..."
# rm -rf $PROJECT_DIR/storage/cache/*

# 4. Riavvio servizi (opzionale, utile per PHP-FPM o Nginx se config cambia)
# echo "Restarting services..."
# sudo systemctl restart php8.2-fpm

echo "--- Deploy Completato con successo! ---"
