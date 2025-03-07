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
echo "** Tag: 6.5.8.16"
echo "** Version: 1.8.0"
echo "** Built: $(cat /build-date.txt)"
echo "** Copyright 2022 dasistweb GmbH"
echo "*******************************************************"
echo ""
echo "launching dockware...please wait..."
echo ""

set -e

source /etc/apache2/envvars
source /var/www/.bashrc

# this is important to automatically use the bashrc file
# in the "exec" command below when using a simple docker runner command
export BASH_ENV=/var/www/.bashrc

CONTAINER_STARTUP_DIR=$(pwd)

# only do all our stuff
# if we are not in recovery mode
if [ $RECOVERY_MODE = 0 ]; then

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
    
    
        if [ $FILEBEAT_ENABLED = 1 ]; then
       echo "DOCKWARE: activating Filebeat..."
       sudo service filebeat start --strict.perms=false
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
        sudo sed -i 's/DEFINER=`root`@`%`/DEFINER=`'$MYSQL_USER'`@`%`/g' /tmp/triggers.sql
        sudo mysql --user=root --password=root shopware < /tmp/triggers.sql
        sudo rm -rf /tmp/triggers.sql
        # -----------------------------------
        # block remote access for root user
        sudo mysql --user=root --password=root -e "DELETE FROM mysql.user WHERE User='root' AND Host='%';";
        # -----------------------------------
        # add new user and grant privileges
        sudo mysql --user=root --password=root -e "CREATE USER IF NOT EXISTS '"$MYSQL_USER"'@'%' IDENTIFIED BY '"$MYSQL_PWD"';";
        sudo mysql --user=root --password=root -e "use mysql; update user set host='%' where user='$MYSQL_USER';";
        sudo mysql --user=root --password=root -e "GRANT ALL PRIVILEGES ON *.* TO '"$MYSQL_USER"'@'%'";
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
    
    

    # --------------------------------------------------
    # APACHE
    # first set the correct doc root, because we need it for the php switch below
    sudo sed -i 's#__dockware_apache_docroot__#'${APACHE_DOCROOT}'#g' /etc/apache2/sites-enabled/000-default.conf
    # --------------------------------------------------

        echo "DOCKWARE: switching to PHP ${PHP_VERSION}..."
    cd /var/www && make switch-php version=${PHP_VERSION}
    sudo service apache2 stop
    echo "-----------------------------------------------------------"
    

    

            if [ $XDEBUG_ENABLED = 1 ]; then
       sh /var/www/scripts/bin/xdebug_enable.sh
     else
       sh /var/www/scripts/bin/xdebug_disable.sh
    fi
    

        if [ $TIDEWAYS_KEY != "not-set" ]; then
        echo "DOCKWARE: activating Tideways...."
        sudo sed -i 's/__DOCKWARE_VAR_TIDEWAYS_ENV__/'${TIDEWAYS_ENV}'/g' /etc/default/tideways-daemon
        sudo sed -i 's/__DOCKWARE_VAR_TIDEWAYS_API_KEY__/'${TIDEWAYS_KEY}'/g' /etc/php/$PHP_VERSION/fpm/conf.d/20-tideways.ini
        sudo sed -i 's/__DOCKWARE_VAR_TIDEWAYS_API_KEY__/'${TIDEWAYS_KEY}'/g' /etc/php/$PHP_VERSION/cli/conf.d/20-tideways.ini
        nohup sudo tideways-daemon --log=/var/log/tideways/daemon.log > /dev/null 2>&1 &
        echo "-----------------------------------------------------------"
    else
        echo "DOCKWARE: Tideways not activated. Disabling..."
        if [ -f /etc/php/$PHP_VERSION/fpm/conf.d/20-tideways.ini ]; then
            sudo mv /etc/php/$PHP_VERSION/fpm/conf.d/20-tideways.ini /etc/php/$PHP_VERSION/fpm/conf.d/20-tideways.disabled
        fi
        if [ -f /etc/php/$PHP_VERSION/cli/conf.d/20-tideways.ini ]; then
            sudo mv /etc/php/$PHP_VERSION/cli/conf.d/20-tideways.ini /etc/php/$PHP_VERSION/cli/conf.d/20-tideways.disabled
        fi
    fi
    

        if [[ ! -z "$NODE_VERSION" ]]; then
       echo "DOCKWARE: switching to Node ${NODE_VERSION}..."
       nvm alias default ${NODE_VERSION}
       # now make sure to at least have node and npm as sudo
       # nvm itself is not possible by design
       sudo rm -f /usr/local/bin/node
       sudo rm -f /usr/local/bin/npm
       sudo ln -s "$(which node)" "/usr/local/bin/node"
       sudo ln -s "$(which npm)" "/usr/local/bin/npm"
       echo "-----------------------------------------------------------"
    fi
    

    
        if [ $SHOP_DOMAIN != "localhost" ]; then
      # update our domain. this means we can use the
        # SHOP DOMAIN as environment variable
        echo "DOCKWARE: updating domain to ${SHOP_DOMAIN}..."
        sh /var/www/scripts/shopware6/update_domain.sh
        echo "-----------------------------------------------------------"
    fi
    
    if [ $SW_CURRENCY != "not-set" ]; then
      echo "DOCKWARE: Switching Shopware default currency..."
      php /var/www/scripts/shopware6/set_currency.php $SW_CURRENCY
      echo "-----------------------------------------------------------"
    fi

    # The access key needs SWSC as a prefix, otherwise shopware
    # won't be able to access the storefront api.
    # The PHP script will automatically add it as a prefix, if
    # it was not defined in the environment variable.
    # The result "ACTUAL_API_ACCESS_KEY" contains the change.
    # It will be echoed at the end of the entrypoint, so the
    # user can see the actual api key they need to use.
    if [ $SW_API_ACCESS_KEY != "not-set" ]; then
      echo "DOCKWARE: Set Shopware API access key..."
      ACTUAL_API_ACCESS_KEY=$(php /var/www/scripts/shopware6/set_api_access_key.php $SW_API_ACCESS_KEY)
      echo "-----------------------------------------------------------"
    fi

    if [ $SW_TASKS_ENABLED = 1 ]; then
      echo "DOCKWARE: creating CRONs for scheduled tasks..."
        crontab /var/www/scripts/cron/crontab.txt && sudo service cron restart
      echo "-----------------------------------------------------------"
    fi

    

    # --------------------------------------------------
    # APACHE
    # sometimes the internal docker structure leaves
    # some pid files existing. the container will be recreated....but
    # in reality it's not! thus there might be the problem
    # that an older pid file exists, which leads to the following error:
    #   - "httpd (pid 13) already running"
    # to avoid this, we simple remove an existing file
    sudo rm -f /var/run/apache2/apache2.pid
    # also, sometimes port 80 is used? happens if you have lots of local containers i think
    # so let's just kill that, otherwise the container won't start
    sudo lsof -t -i tcp:80 | sudo xargs kill >/dev/null 2>&1 || true;

    # start test and start apache
    echo "DOCKWARE: testing and starting Apache..."
    sudo apache2ctl configtest
    sudo service apache2 restart
    echo "-----------------------------------------------------------"
    # --------------------------------------------------

    # before starting any commands
    # we always need to ensure we are back in our
    # configured WORKDIR of the container
    echo "-----------------------------------------------------"
    cd $CONTAINER_STARTUP_DIR

    # now let's check if we have a custom boot script that
    # should run after our other startup scripts.
    file="/var/www/boot_end.sh"
    if [ -f "$file" ] ; then
        sh $file
    fi

    # ------------------------------------------------------------------------------------------------------------------------------
    # ------------------------------------------------------------------------------------------------------------------------------
    # ------------------------------------------------------------------------------------------------------------------------------
    # ------------------------------------------------------------------------------------------------------------------------------
    # ------------------------------------------------------------------------------------------------------------------------------

        echo ""
    echo "WOHOOO, dockware/dev:6.5.8.16 IS READY :) - let's get started"
    echo "-----------------------------------------------------"
    echo "DOCKWARE CHANGELOG: /var/www/CHANGELOG.md"
    echo "PHP: $(php -v | grep cli)"
    echo "Apache DocRoot: ${APACHE_DOCROOT}"

    echo "URLs (if you are using a custom domain, make sure its available using /etc/hosts or other approaches)"
        echo "ADMINER URL: http://${SHOP_DOMAIN}/adminer.php"
    
        echo "MAILCATCHER URL: http://${SHOP_DOMAIN}/mailcatcher"
    
        echo "PIMPMYLOG URL: http://${SHOP_DOMAIN}/logs"
    
        echo "SHOP URL: http://${SHOP_DOMAIN}"
        echo "ADMIN URL: http://${SHOP_DOMAIN}/admin"
            if [ $SW_API_ACCESS_KEY != "not-set" ]; then
        echo "ACCESS KEY: ${ACTUAL_API_ACCESS_KEY}"
    fi
    
    echo ""
    echo "What's new in this version? see the changelog for further details"
    echo "https://www.shopware.com/de/changelog/"
    echo ""
    
    

    # ------------------------------------------------------------------------------------------------------------------------------
    # ------------------------------------------------------------------------------------------------------------------------------
    # ------------------------------------------------------------------------------------------------------------------------------
    # ------------------------------------------------------------------------------------------------------------------------------
    # ------------------------------------------------------------------------------------------------------------------------------

    
if [[ -z "${BUILD_PLUGIN}" ]]; then
    echo ""
else
    echo "STARTING IN PLUGIN BUILDING MODE...."
    echo "DOCKWARE WILL NOW BUILD YOUR PLUGIN AND EXIT THE CONTAINER AFTERWARDS"
    echo ""
    # if we are in the plugin build mode, then
    # make sure to run our commands to  install and activate it.
    # afterwards build the javascript
        cd /var/www/html && php bin/console plugin:refresh && \
    cd /var/www/html && php bin/console plugin:install --activate "${BUILD_PLUGIN}"
    cd /var/www/html && ./bin/build-js.sh
# set the CI mode to ON, so that our container exists after
# installing and building our plugin
    export DOCKWARE_CI=1
    fi

    # used to inject the custom build script of
    # plugins in dockware/dev
    

else

    echo ""
    echo "Dockware has been started in RECOVERY_MODE."
    echo "Nothing has been executed or initialized..."
    echo ""

    # build the recovery mode file (for SVRUnit Tests
    echo "enabled" > /var/www/recovery.txt

fi

# always execute custom commands in here.
# if a custom command is provided, then the container
# will automatically exit after it.
# that's somehow just how it works.
# otherwise it will continue with the code below
exec "$@"

# we still need this to allow custom events
# such as our BUILD_PLUGIN feature to exit the container
if [[ ! -z "$DOCKWARE_CI" ]]; then
    # CONTAINER WAS STARTED IN NON-BLOCKING CI MODE...."
    # DOCKWARE WILL NOW EXIT THE CONTAINER"
    echo ""
else
    tail -f /dev/null
fi

