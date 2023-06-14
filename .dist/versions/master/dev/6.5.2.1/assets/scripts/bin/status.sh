echo apache2 status:
service apache2 status

echo ""
echo "-----------------------------------------------"
echo 'php Version: ' ${PHP_VERSION}

echo ""
echo "-----------------------------------------------"
echo 'fpm-Status: '
service php${PHP_VERSION}-fpm status

echo ""
echo "-----------------------------------------------"
echo 'php-cli infos' && php -v
