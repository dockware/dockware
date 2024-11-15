# Official Dockware Image
# Tag: latest
# Copyright 2022 dasistweb GmbH
#
FROM ubuntu:23.10

LABEL title="Dockware dev:latest" \
      version="1.7.0" \
      maintainer="dasistweb GmbH"

# remember build-date
RUN date >/build-date.txt && \
    mkdir -p /var/www && \
    mkdir -p /var/www/scripts

# add our changelog to the containers
ADD ./assets/CHANGELOG.md /var/www/CHANGELOG.md



## ***********************************************************************

RUN apt-get update && apt-get install -y --no-install-recommends \
    software-properties-common \
    curl \
    vim \
    git \
    jq \
    unzip \
    mysql-server \
    php8.2 \
    php8.2-cli \
    php8.2-fpm \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-curl \
    php8.2-zip \
    php8.2-intl \
    php8.2-mysql \
    php8.2-gd \
    php8.2-bcmath && \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENV MYSQL_ROOT_PASSWORD=root


# Start MySQL, change authentication method, and configure permissions
RUN service mysql start && \
    sleep 5 && \
    mysql --user=root --execute="ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root'; FLUSH PRIVILEGES;" && \
    service mysql stop

# TODO 300 MB
# RUN sudo service mysql start && \
 # switch to default PHP before installing
    #sudo update-alternatives --set php /usr/bin/php8.3 > /dev/null 2>&1 && \
    # -------------------------------------------------------------------------------------------
RUN service mysql start && \
    cd /var/www && \
    composer create-project shopware/production:6.6.8.2 --no-interaction tmp && \
    composer require --dev shopware/dev-tools && \
    cp -a /var/www/tmp/. /var/www/html && \
    rm -rf /var/www/tmp  && \
#    # -------------------------------------------------------------------------------------------
    # ALWAYS UPDATE .env itself
    # if we would use .env.local, then these things could happen....our 127.0.0.1 instead of localhost for MySQL might not exist in a users custom .env.local
    # which would use Shopware default with localhost, which breaks the DB connection in MySQL 8
    sed -i "/APP_ENV=/g" /var/www/html/.env && \
    sed -i "/APP_URL=/g" /var/www/html/.env && \
    sed -i "/DATABASE_URL=/g" /var/www/html/.env && \
    sed -i "/MAILER_URL=/g" /var/www/html/.env && \
    echo "APP_ENV=dev" >> /var/www/html/.env && \
    echo "APP_URL=http://localhost" >> /var/www/html/.env && \
    echo "DATABASE_URL=mysql://root:root@127.0.0.1:3306/shopware" >> /var/www/html/.env && \
    echo "MAILER_DSN=smtp://127.0.0.1:1025" >> /var/www/html/.env  && \
    # -------------------------------------------------------------------------------------------
    cd /var/www/html && php bin/console system:install --create-database --basic-setup && \
    cd /var/www/html && php bin/console assets:install && \
    # -------------------------------------------------------------------------------------------
    cd /var/www/html && APP_ENV=prod php bin/console store:download -p SwagPlatformDemoData && \
    cd /var/www/html && APP_ENV=prod php bin/console plugin:refresh && \
    cd /var/www/html && APP_ENV=prod php bin/console plugin:install --activate SwagPlatformDemoData && \
    # -------------------------------------------------------------------------------------------
    #cd /var/www/html && php bin/console plugin:install DockwareSamplePlugin && \
    #cd /var/www/html && php bin/console plugin:activate DockwareSamplePlugin && \
    # -------------------------------------------------------------------------------------------
    # clear cache and refresh dal index to show the new demo data
    cd /var/www/html && php bin/console cache:clear && \
    cd /var/www/html/bin && ./build-storefront.sh && \
    cd /var/www/html && php bin/console theme:change --all --no-compile --no-interaction Storefront && \
    cd /var/www/html && php bin/console theme:compile && \
    cd /var/www/html && php bin/console dal:refresh:index && \
    rm -rf /var/www/html/var/cache/* && \
    # -------------------------------------------------------------------------------------------
    mysql --user=root --password=root -e "use shopware; INSERT INTO system_config (id, configuration_key, configuration_value, sales_channel_id, created_at, updated_at) VALUES (X'b3ae4d7111114377af9480c4a0911111', 'core.frw.completedAt', '{\"_value\": \"2019-10-07T10:46:23+00:00\"}', NULL, '2019-10-07 10:46:23.169', NULL);" && \
    # -------------------------------------------------------------------------------------------
    service mysql stop


## ***********************************************************************










ADD entrypoint.sh /entrypoint.sh

## ***********************************************************************
## SWITCH TO NORMAL USER (NOT ROOT ANYMORE!)
## everything down here is now done as our www-data / dockware user
## just like you would do it manually in the container
## ***********************************************************************
USER www-data

# make the apache folder the working directory
WORKDIR /var/www/html

## ***********************************************************************
##  POST BUILD
## ***********************************************************************

ENTRYPOINT ["/bin/bash", "/entrypoint.sh"]
