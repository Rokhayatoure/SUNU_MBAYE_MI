sleep 10
# chown -R mysql:mysql /var/run/mysqld
php artisan migrate --force
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
apache2-foreground