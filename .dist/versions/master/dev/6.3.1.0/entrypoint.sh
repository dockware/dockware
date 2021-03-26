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
echo "** DOCKWARE IMAGE: dev"
echo "** Tag: 6.3.1.0"
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

if [ $XDEBUG_ENABLED = 1 ]; then
   sh /var/www/scripts/bin/xdebug_enable.sh
 else
   sh /var/www/scripts/bin/xdebug_disable.sh
fi

if [ $FILEBEAT_ENABLED = 1 ]; then
   echo "DOCKWARE: activating Filebeat..."
   sudo service filebeat start --strict.perms=false
   echo "-----------------------------------------------------------"
fi

if [ $COMPOSER_VERSION = 1 ]; then
   echo "DOCKWARE: switching to composer 1..."
   sudo composer self-update --1
   echo "-----------------------------------------------------------"
fi
if [ $COMPOSER_VERSION = 2 ]; then
   echo "DOCKWARE: switching to composer 2..."
   sudo composer self-update --stable
   echo "-----------------------------------------------------------"
fi

if [ $TIDEWAYS_KEY != "not-set" ]; then
    echo "DOCKWARE: activating Tideways...."
    sudo sed -i 's/__DOCKWARE_VAR_TIDEWAYS_ENV__/'${TIDEWAYS_ENV}'/g' /etc/default/tideways-daemon
    sudo sed -i 's/__DOCKWARE_VAR_TIDEWAYS_API_KEY__/'${TIDEWAYS_KEY}'/g' /etc/php/$PHP_VERSION/fpm/conf.d/20-tideways.ini
    sudo sed -i 's/__DOCKWARE_VAR_TIDEWAYS_SERVICE__/'${TIDEWAYS_SERVICE}'/g' /etc/php/$PHP_VERSION/fpm/conf.d/20-tideways.ini
    sudo sed -i 's/__DOCKWARE_VAR_TIDEWAYS_API_KEY__/'${TIDEWAYS_KEY}'/g' /etc/php/$PHP_VERSION/cli/conf.d/20-tideways.ini
    sudo sed -i 's/__DOCKWARE_VAR_TIDEWAYS_SERVICE__/'${TIDEWAYS_SERVICE}'/g' /etc/php/$PHP_VERSION/cli/conf.d/20-tideways.ini
    sudo service tideways-daemon start
    echo "-----------------------------------------------------------"
fi

# checks if a different username is set in ENV and create if its not existing yet
if [ $SSH_USER != "not-set" ] && (! id -u "${SSH_USER}" >/dev/null 2>&1 ); then
    echo "DOCKWARE: creating additional SSH user...."
    # create a custom ssh user for our provided settings
    sudo adduser --disabled-password --uid 8888 --gecos "" --ingroup www-data $SSH_USER
    sudo usermod -a -G sudo $SSH_USER
    sudo usermod -m -d /var/www $SSH_USER | true
    sudo echo "${SSH_USER}:${SSH_PWD}" | sudo chpasswd
    sudo sed -i "s/${SSH_USER}:x:8888:33:/${SSH_USER}:x:33:33:/g" /etc/passwd
    # add sudo without password
    # write user to file cause we loos the var as we executing as root and get a new shell
    sudo echo "${SSH_USER}" >> /tmp/user.name
    sudo -u root sh -c 'echo "Defaults:$(cat /tmp/user.name) !requiretty" >> /etc/sudoers'
    sudo rm -rf /tmp/user.name
    # disable original ssh access
    sudo usermod -s /bin/false dockware
    # allow ssh in sshd_config
    sudo sed -i "s/AllowUsers dockware/AllowUsers ${SSH_USER}/g" /etc/ssh/sshd_config
    echo "-----------------------------------------------------------"
fi

# start the SSH service with the latest setup
echo "DOCKWARE: restarting SSH service...."
sudo service ssh restart
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

if [ $MYSQL_USER != "not-set" ] && [ $MYSQL_PWD != "not-set" ]; then
    echo "DOCKWARE: creating new MySQL user...."
    # -----------------------------------
    # Shopware users triggers. the DEFINER does also need to be changed to our new user
    # otherwise problems like "product cant be created in admin" will occur.
    # the only solution is to export the triggers, replace the DEFINER and import it again.
    sudo mysqldump -P 3306 -h localhost -u root -p"root" --triggers --add-drop-trigger --no-create-info --no-data --no-create-db --skip-opt shopware > /tmp/triggers.sql
    sudo sed -i 's/DEFINER=`root`@`%`/DEFINER=`app`@`%`/g' /tmp/triggers.sql
    sudo mysql --user=root --password=root shopware < /tmp/triggers.sql
    sudo rm -rf /tmp/triggers.sql
    # -----------------------------------
    # block remote access for root user
    sudo mysql --user=root --password=root -e "UPDATE mysql.user SET Host='localhost' WHERE User='root' AND Host='%';";
    # -----------------------------------
    # add new user and grant privileges
    sudo mysql --user=root --password=root -e "CREATE USER IF NOT EXISTS '"$MYSQL_USER"'@'%' IDENTIFIED BY '"$MYSQL_PWD"';";
    sudo mysql --user=root --password=root -e "use mysql; update user set host='%' where user='$MYSQL_USER';";
    sudo mysql --user=root --password=root -e "GRANT ALL PRIVILEGES ON *.* TO '"$MYSQL_USER"'@'%' IDENTIFIED BY '$MYSQL_PWD';";
    # -----------------------------------
    # apply and flush privileges
    sudo mysql --user=root --password=root -e "FLUSH PRIVILEGES;";
    echo "-----------------------------------------------------------"
fi
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
echo "WOHOOO, dockware/dev:6.3.1.0 IS READY :) - let's get started"
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
