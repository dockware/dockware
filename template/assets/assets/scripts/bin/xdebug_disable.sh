echo "DOCKWARE: deactivating Xdebug..."

sudo mv /etc/php/${PHP_VERSION}/fpm/conf.d/20-xdebug.ini /etc/php/${PHP_VERSION}/fpm/conf.d/20-xdebug.ini_disabled  > /dev/null 2>&1 &
sudo mv /etc/php/${PHP_VERSION}/cli/conf.d/20-xdebug.ini /etc/php/${PHP_VERSION}/cli/conf.d/20-xdebug.ini_disabled   > /dev/null 2>&1 &

sudo service php${PHP_VERSION}-fpm restart
echo "-----------------------------------------------------------"