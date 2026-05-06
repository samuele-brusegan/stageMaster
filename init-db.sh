#!/bin/bash

# Database initialization script for StageMaster
# This script resets the database and creates all tables on first startup

echo "Starting database initialization..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"db" --silent; do
    echo "Waiting for database connection..."
    sleep 2
done

echo "MySQL is ready. Initializing database..."

# Drop and recreate database
mysql -h db -u root -proot_password -e "DROP DATABASE IF EXISTS olmos_talent;"
mysql -h db -u root -proot_password -e "CREATE DATABASE olmos_talent;"
mysql -h db -u root -proot_password -e "USE olmos_talent;"

# Import schema
echo "Importing schema..."
mysql -h db -u root -proot_password olmos_talent < /docker-entrypoint-initdb.d/01-schema.sql

# Import test data if exists
if [ -f "/docker-entrypoint-initdb.d/02-test-data.sql" ]; then
    echo "Importing test data..."
    mysql -h db -u root -proot_password olmos_talent < /docker-entrypoint-initdb.d/02-test-data.sql
fi

echo "Database initialization completed!"
