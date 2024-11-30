#!/bin/bash
set -e

# Run Doctrine commands (only if the database doesn't already exist)
if [ ! -f var/cache/prod/doctrine/metadata.cache.php ]; then
  echo "Creating the database and updating the schema..."
  php bin/console doctrine:database:create
  php bin/console doctrine:schema:update --force
fi

# Now start PHP-FPM or any other command to keep the container running
exec "$@"
