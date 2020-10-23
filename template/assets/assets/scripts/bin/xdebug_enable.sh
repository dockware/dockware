echo "DOCKWARE: activating Xdebug..."

sudo mv /etc/php/${PHP_VERSION}/fpm/conf.d/20-xdebug.ini_disabled /etc/php/${PHP_VERSION}/fpm/conf.d/20-xdebug.ini  > /dev/null 2>&1 &
sudo mv /etc/php/${PHP_VERSION}/cli/conf.d/20-xdebug.ini_disabled /etc/php/${PHP_VERSION}/cli/conf.d/20-xdebug.ini  > /dev/null 2>&1 &

sudo sed -i 's/__dockware_host__/'${XDEBUG_REMOTE_HOST}'/g' /etc/php/${PHP_VERSION}/fpm/conf.d/20-xdebug.ini
sudo sed -i 's/__dockware_host__/'${XDEBUG_REMOTE_HOST}'/g' /etc/php/${PHP_VERSION}/cli/conf.d/20-xdebug.ini

sudo service php${PHP_VERSION}-fpm stop > /dev/null 2>&1 &
sudo service php${PHP_VERSION}-fpm start

echo "------------------------------------------------"