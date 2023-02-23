mysql -h 127.0.0.1 -uroot -proot shopware < /var/www/scripts/shopware6/create_https.sql

mysql -h 127.0.0.1 -uroot -proot -e "use shopware; UPDATE sales_channel_domain SET url = 'http://${SHOP_DOMAIN}' WHERE url like 'http://%';"
mysql -h 127.0.0.1 -uroot -proot -e "use shopware; UPDATE sales_channel_domain SET url = 'https://${SHOP_DOMAIN}' WHERE url like 'https://%';"