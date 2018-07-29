#GitLabCat

<img src="https://i.pinimg.com/originals/1f/8d/c8/1f8dc89daf04c550f088db37e61415ff.jpg" />


__GitLab events, issues, statistics, etc notifications via Telegram bot.__
+ domain expiring monitor
+ services health monitor
+ reminders
 

### Dependencies

- php > 7.1.3
- postgresql
- redis
- whois


```
cp .env.example .env
comopser global require  hirak/prestissimo
composer install

php artisan key:generate

php artisan view:clear && php artisan route:clear && php artisan cache:clear && php artisan config:cache

php artisan migrate --force
php artisan db:seed --force

chown www-data:www-data * -R


```

TODO: 
- i8n
- views
- unit tests