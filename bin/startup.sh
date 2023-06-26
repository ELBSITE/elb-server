#/usr/bin/env bash
certbot run -a dns-route53 --dns-route53-propagation-seconds 10 -i apache -n --register-unsafely-without-email --expand --agree-tos --domains www.energylightbulbs.co.uk,energylightbulbs.co.uk 
service apache2 stop
sudo -u www-data echo -n $ENV_DATA | base64 -d > /var/www/html/app/etc/env.php
sudo -u www-data mv /var/www/html/app/etc/config.example.php /var/www/html/app/etc/config.php
sudo -u www-data /var/www/html/bin/magento setup:di:compile
sudo -u www-data /var/www/html/bin/magento setup:static-content:deploy -f
sudo -u www-data /var/www/html/bin/magento cron:install
rm -rf /usr/local/etc/php/conf.d/custom.ini
apache2-foreground