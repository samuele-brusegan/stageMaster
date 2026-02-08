#!/bin/bash

echo "--- Inizio Deploy Docker ---"

# 1. Pull dei cambiamenti
echo "Pulling latest changes..."
git pull origin main

# 2. Build e riavvio container
echo "Rebuilding and restarting containers..."
docker-compose up -d --build

# 3. Pulizia immagini vecchie (opzionale)
echo "Cleaning up old images..."
docker image prune -f

echo "--- Deploy Docker Completato! ---"
