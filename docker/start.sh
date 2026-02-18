#!/bin/bash
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:mongodb:schema:create --no-interaction
apache2-foreground