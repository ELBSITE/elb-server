#/usr/bin/env bash

echo -n $ENV_DATA | base64 -d > /var/www/html/app/etc/env.php
mv /var/www/html/app/etc/config.example.php /var/www/html/app/etc/config.php
/var/www/html/bin/magento setup:di:compile
/var/www/html/bin/magento setup:static-content:deploy -f
/var/www/html/bin/magento cron:install
apache2-foreground