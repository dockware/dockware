#!/bin/bash
echo ""
echo " _____   ____   _____ _  ____          __     _____  ______ "
echo "|  __ \ / __ \ / ____| |/ /\ \        / /\   |  __ \|  ____|"
echo "| |  | | |  | | |    | ' /  \ \  /\  / /  \  | |__) | |__   "
echo "| |  | | |  | | |    |  <    \ \/  \/ / /\ \ |  _  /|  __|  "
echo "| |__| | |__| | |____| . \    \  /\  / ____ \| | \ \| |____ "
echo "|_____/ \____/ \_____|_|\_\    \/  \/_/    \_\_|  \_\______|"
echo ""
echo "68 69 20 64 65 76 65 6C 6F 70 65 72 2C 20 6E 69 63 65 20 74 6F 20 6D 65 65 74 20 79 6F 75"
echo "6c 6f 6f 6b 69 6e 67 20 66 6f 72 20 61 20 6a 6f 62 3f 20 77 72 69 74 65 20 75 73 20 61 74 20 6a 6f 62 73 40 64 61 73 69 73 74 77 65 62 2e 64 65"
echo ""
echo "*******************************************************"
echo "** DOCKWARE IMAGE: play"
echo "** Tag: 6.3.0.2"
echo "** Version: 1.3.6-beta"
echo "** Built: $(cat /build-date.txt)"
echo "** Copyright 2020 dasistweb GmbH"
echo "*******************************************************"
echo ""
echo "launching dockware...please wait..."
echo ""

set -e

source /etc/apache2/envvars

# it's possible to add a custom boot script on startup.
# so we test if it exists and just execute it
file="/var/www/boot_start.sh"
if [ -f "$file" ] ; then
    sh $file
fi

echo "DOCKWARE: setting timezone to ${TZ}..."
sudo ln -sf /usr/share/zoneinfo/${TZ} /etc/localtime
sudo dpkg-reconfigure -f noninteractive tzdata
echo "-----------------------------------------------------------"

echo "DOCKWARE: starting MySQL...."
# somehow its necessary to set permissions, because
# sometimes they get lost :)
# make sure that it is no longer present from the last run
file="/var/run/mysqld/mysqld.sock.lock"
if [ -f "$file" ] ; then
    sudo rm -f "$file"
fi

sudo chown -R mysql:mysql /var/lib/mysql /var/run/mysqld
sudo service mysql start;

echo "-----------------------------------------------------------"

echo "DOCKWARE: starting mailcatcher...."
sudo /usr/bin/env $(which mailcatcher) --ip=0.0.0.0
echo "-----------------------------------------------------------"

echo "DOCKWARE: starting cron service...."
sudo service cron start
echo "-----------------------------------------------------------"

echo "DOCKWARE: switching to PHP ${PHP_VERSION}..."
sudo sed -i 's/__dockware_php_version__/'${PHP_VERSION}'/g' /etc/apache2/sites-enabled/000-default.conf
sudo service php${PHP_VERSION}-fpm stop > /dev/null 2>&1
sudo service php${PHP_VERSION}-fpm start
sudo update-alternatives --set php /usr/bin/php${PHP_VERSION} > /dev/null 2>&1 &
echo "-----------------------------------------------------------"

if [ $SW_CURRENCY != "not-set" ]; then
  echo "DOCKWARE: Switching Shopware default currency..."
  php /var/www/scripts/shopware6/set_currency.php $SW_CURRENCY
  echo "-----------------------------------------------------------"
fi

# --------------------------------------------------
# APACHE
sudo sed -i 's#__dockware_apache_docroot__#'${APACHE_DOCROOT}'#g' /etc/apache2/sites-enabled/000-default.conf

# sometimes the internal docker structure leaves
# some pid files existing. the container will be recreated....but
# in reality it's not! thus there might be the problem
# that an older pid file exists, which leads to the following error:
#   - "httpd (pid 13) already running"
# to avoid this, we simple remove an existing file
sudo rm -f /var/run/apache2/apache2.pid

# start test and start apache
echo "DOCKWARE: testing and starting Apache..."
sudo apache2ctl configtest
sudo service apache2 restart
echo "-----------------------------------------------------------"
# --------------------------------------------------

# now let's check if we have a custom boot script that
# should run after our other startup scripts.
file="/var/www/boot_end.sh"
if [ -f "$file" ] ; then
    sh $file
fi

echo ""
echo "WOHOOO, dockware/play:6.3.0.2 IS READY :) - let's get started"
echo "-----------------------------------------------------"
echo "DOCKWARE CHANGELOG: /var/www/CHANGELOG.md"
echo "PHP: $(php -v | grep cli)"
echo "Apache DocRoot: ${APACHE_DOCROOT}"

echo "ADMINER URL: http://localhost/adminer.php"

echo "MAILCATCHER URL: http://localhost/mailcatcher"

echo "PIMPMYLOG URL: http://localhost/logs"

echo "SHOP URL: http://localhost"
echo "ADMIN URL: http://localhost/admin"

echo ""
echo "What's new in this version? see the changelog for further details"
echo "https://www.shopware.com/de/changelog/"
echo ""

tail -f /dev/null
