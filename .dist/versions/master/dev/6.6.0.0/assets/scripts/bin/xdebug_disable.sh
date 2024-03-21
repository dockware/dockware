echo "DOCKWARE: deactivating Xdebug..."

#make sure we use the current running php version and not that one from the ENV
PHP_VERSION_RUNNING=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')

sudo mv /etc/php/${PHP_VERSION_RUNNING}/fpm/conf.d/20-xdebug.ini /etc/php/${PHP_VERSION_RUNNING}/fpm/conf.d/20-xdebug.ini_disabled  > /dev/null 2>&1 &
sudo mv /etc/php/${PHP_VERSION_RUNNING}/cli/conf.d/20-xdebug.ini /etc/php/${PHP_VERSION_RUNNING}/cli/conf.d/20-xdebug.ini_disabled  > /dev/null 2>&1 &
wait

sudo service php${PHP_VERSION_RUNNING}-fpm restart > /dev/null 2>&1 &