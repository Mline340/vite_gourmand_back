#!/bin/bash

# Extraire les infos de connexion depuis DATABASE_URL
# Format: mysql://user:password@host:port/dbname
DB_URL=$DATABASE_URL
DB_USER=$(echo $DB_URL | sed 's/.*:\/\/\([^:]*\):.*/\1/')
DB_PASSWORD=$(echo $DB_URL | sed 's/.*:\/\/[^:]*:\([^@]*\)@.*/\1/')
DB_HOST=$(echo $DB_URL | sed 's/.*@\([^:]*\):.*/\1/')
DB_PORT=$(echo $DB_URL | sed 's/.*@[^:]*:\([^/]*\)\/.*/\1/')
DB_NAME=$(echo $DB_URL | sed 's/.*\/\([^?]*\).*/\1/')

# Migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Import des données SQL
mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p$DB_PASSWORD $DB_NAME < /var/www/html/docker/sql/database_docker.sql

# MongoDB
php bin/console doctrine:mongodb:schema:create --no-interaction

apache2-foreground