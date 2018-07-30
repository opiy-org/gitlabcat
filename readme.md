# GitLabCat

<img src="https://i.pinimg.com/originals/1f/8d/c8/1f8dc89daf04c550f088db37e61415ff.jpg" />


__GitLab events, issues, statistics, etc notifications via Telegram bot.__
+ domain expiring monitor
+ services health monitor
+ reminders
 

### Dependencies:

- php > 7.1.3
- postgresql / mysql
- redis (optional)
- whois


### Install: 

```
cd /path-to-your-project
cp .env.example .env
vim .env

composer global require  hirak/prestissimo
composer install

php artisan key:generate

php artisan view:clear && php artisan route:clear && php artisan cache:clear && php artisan config:cache

php artisan migrate --force

chown www-data:www-data * -R

crontab -l 
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Create domain, nginx vhost, config https on it and reg tg webhook:
```
php artisan regtgwh
```

Go to gitlab LINK/yourproject/settings/integrations
and create new gitlab webhook to https://your site/wh



TODO: 
- i8n
- views
- unit tests