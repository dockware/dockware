# Official Dockware Image
# Tag: 6.3.5.0
# Copyright 2022 dasistweb GmbH
#
FROM ubuntu:18.04
LABEL title="Dockware play:6.3.5.0" \
      version="1.7.0" \
      maintainer="dasistweb GmbH"

# remember build-date
RUN date >/build-date.txt && \
    mkdir -p /var/www && \
    mkdir -p /var/www/scripts

# add our changelog to the containers
ADD ./assets/CHANGELOG.md /var/www/CHANGELOG.md

## ***********************************************************************
##  IMAGE VARIABLES
## ***********************************************************************
ENV TZ Europe/Berlin
ENV PHP_VERSION 7.4
ENV APACHE_DOCROOT /var/www/html/public
ENV SW_TASKS_ENABLED 0
ENV COMPOSER_VERSION not-set
ENV NODE_VERSION 12
ENV SHOP_DOMAIN localhost
ENV RECOVERY_MODE 0
RUN echo "export TZ=${TZ}" >> /etc/profile \
 && echo "export PHP_VERSION=${PHP_VERSION}" >> /etc/profile \
 && echo "export APACHE_DOCROOT=${APACHE_DOCROOT}" >> /etc/profile \
 && echo "export SW_TASKS_ENABLED=${SW_TASKS_ENABLED}" >> /etc/profile \
 && echo "export COMPOSER_VERSION=${COMPOSER_VERSION}" >> /etc/profile \
 && echo "export NODE_VERSION=${NODE_VERSION}" >> /etc/profile \
 && echo "export SHOP_DOMAIN=${SHOP_DOMAIN}" >> /etc/profile \
 && echo "export RECOVERY_MODE=${RECOVERY_MODE}" >> /etc/profile

ENV SW_CURRENCY 'not-set'
RUN echo "export SW_CURRENCY=${SW_CURRENCY}" >> /etc/profile

ENV SW_API_ACCESS_KEY 'not-set'
RUN echo "export SW_API_ACCESS_KEY=${SW_API_ACCESS_KEY}" >> /etc/profile

## ***********************************************************************
##  BASE REQUIREMENTS
## ***********************************************************************
RUN apt-get update \
        && apt-get install -y gosu \
    && apt-get install -y sudo \
    && apt-get install -y wget \
    && apt-get install -y curl \
    && apt-get install -y unzip \
    && apt-get install -y bzip2 \
    && apt-get install -y ssmtp \
    && apt-get install -y lsof \
    && apt-get install -y openssh-server \
    && apt-get install -y cron \
    && apt-get install -y vim \
    && apt-get install -y nano \
    && apt-get install -y jq \
    && apt-get install -y gnupg2 \
    && apt-get install -y gpg-agent \
    && apt-get install -y chromium-browser \
    && mkdir /var/run/sshd \
    # TIMEZONE SETTINGS
    # otherwise we would have an interactive input dialog
    && ln -fs /usr/share/zoneinfo/Europe/Berlin /etc/localtime \
        && apt-get install -y tzdata \
    && dpkg-reconfigure --frontend noninteractive tzdata  \
     \
        && apt-get install -y xdg-utils \
            && apt-get install -y libsodium-dev \
    && apt-get install -y php-dev \
    && apt-get install -y php-pear \
    && pecl install -f libsodium \
    && apt-get remove -y php-pear \
    && apt-get remove -y php-dev \
            && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

## ***********************************************************************
##  USER MANAGEMENT
## ***********************************************************************
# set easy root pwd for dev purpose
RUN echo "root:root" | chpasswd \
    # set password for www-data, and also
    # avoid shell login (we have a separate user for that)
    && echo 'www-data:www-data' | chpasswd \
    && usermod -s /usr/sbin/nologin www-data \
    # this option makes sure to avoid root SSH login
    # we just replace our lines with nothing
    && sed -i 's/PermitRootLogin without-password//' /etc/ssh/sshd_config \
    && sed -i 's/PermitRootLogin prohibit-password//' /etc/ssh/sshd_config \
    # allow root and sudo group to run sudo without password
    && sed -i /etc/sudoers -re 's/^%sudo.*/%sudo ALL=(ALL:ALL) NOPASSWD: ALL/g' \
    && sed -i /etc/sudoers -re 's/^root.*/root ALL=(ALL:ALL) NOPASSWD: ALL/g' \
    # remove include directory
    && sed -i /etc/sudoers -re 's/^#includedir.*/## **Removed the include directive** ##"/g'

## ***********************************************************************
## creates a new user as www-data alias and adds it to the sudo group
## along with privileges to run sudo without password
## params:
#       - string user
#       - string pwd
#       - bool sudo
## ***********************************************************************
RUN adduser --disabled-password --uid 5577 --gecos "" --ingroup www-data dockware \
    && usermod -m -d /var/www dockware | true \
    && echo "dockware:dockware" | chpasswd \
        && usermod -a -G sudo dockware \
    # allow sudo without pwd and dont require tty (for entrypoint commands)
    && echo "Defaults:dockware !requiretty" >> /etc/sudoers \
        && sed -i 's/dockware:x:5577:33:/dockware:x:33:33:/g' /etc/passwd
RUN echo 'AllowUsers dockware' >> /etc/ssh/sshd_config

ENV BASH_ENV /var/www/.bashrc

RUN echo "source /var/www/.nvm/nvm.sh" >> /var/www/.bashrc \
    # -------------------------------------------------
    && chown 33:33 /var/www/.bashrc \
    # -------------------------------------------------
    && echo "export BASH_ENV=${BASH_ENV}" >> /etc/profile

## ***********************************************************************
##  APACHE INSTALLATION
## ***********************************************************************
#this conf is needed for enconf command ...
ADD ./config/apache/http2.conf /etc/apache2/conf-available/http2.conf

RUN apt-get update \
    && apt-get install -y apache2 \
    && apt-get install -y libapache2-mod-fcgid \
    && apt-get install -y software-properties-common \
    && LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php \
    && a2enmod headers \
    && a2enmod rewrite \
    && a2enmod expires \
    && a2enmod proxy \
    && a2enmod proxy_http \
    && a2enmod proxy_wstunnel \
    && a2enmod actions \
    && a2enmod fcgid \
    && a2enmod alias \
    && a2enmod proxy_fcgi \
    && a2enmod http2 \
    && sudo a2enconf http2 \
    && sudo a2dismod mpm_prefork > /dev/null 2>&1 \
    && sudo a2enmod mpm_event > /dev/null 2>&1 \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

ADD ./config/apache/apache2.conf /etc/apache2/apache2.conf
ADD ./config/apache/ports.conf /etc/apache2/ports.conf
ADD ./config/apache/sites.conf /etc/apache2/sites-enabled/000-default.conf

# create a new id_rsa for the www-data dockeruser.
# thus we have a SSH Key for connections out of the box

RUN mkdir -p /var/www/.ssh \
    && rm -rf /var/www/.ssh/id_rsa; true  \
    && rm -rf /var/www/.ssh/id_rsa.pub; true  \
    && ssh-keygen -t rsa -b 4096 -f /var/www/.ssh/id_rsa -C "Dockware Container" -P ""  \
    && chown -R www-data:www-data /var/www/.ssh \
    && chmod 0700 /var/www/.ssh

## ***********************************************************************
##  PHP INSTALLATION
## ***********************************************************************
RUN apt-get update \
    && apt-get install -y php8.2-fpm \
    && apt-get install -y php8.2-gd \
    && apt-get install -y php8.2-iconv \
    && apt-get install -y php8.2-intl \
    && apt-get install -y php8.2-xml \
    && apt-get install -y php8.2-mbstring \
    && apt-get install -y php8.2-pdo \
    && apt-get install -y php8.2-mysql \
    && apt-get install -y php8.2-curl \
    && apt-get install -y php8.2-cli \
    && apt-get install -y php8.2-ldap \
    && apt-get install -y php8.2-pgsql \
    && apt-get install -y php8.2-gettext \
    && apt-get install -y php8.2-zip \
    && apt-get install -y php8.2-soap \
    && apt-get install -y php8.2-bcmath \
    && apt-get install -y php8.2-imap \
    && apt-get install -y php8.2-redis \
    && apt-get install -y php8.2-amqp \
    && apt-get install -y php8.2-apcu \
    && apt-get install -y php8.2-pcov \
    && apt-get install -y php8.2-mongo \
    && apt-get install -y dh-php \
    # shopware required pcre
    && apt-get install -y libpcre3 libpcre3-dev \
&& cat /tmp/general.ini >| /etc/php/8.2/fpm/conf.d/01-general.ini \
&& cat /tmp/general.ini >| /etc/php/8.2/cli/conf.d/01-general.ini \
&& cat /tmp/cli.ini >| /etc/php/8.2/cli/conf.d/01-general-cli.ini \
    && apt-get install -y php8.1-fpm \
    && apt-get install -y php8.1-gd \
    && apt-get install -y php8.1-iconv \
    && apt-get install -y php8.1-intl \
    #&& apt-get install -y php8.1-json \
    && apt-get install -y php8.1-xml \
    && apt-get install -y php8.1-mbstring \
    && apt-get install -y php8.1-pdo \
    && apt-get install -y php8.1-mysql \
    && apt-get install -y php8.1-apc \
    && apt-get install -y php8.1-apcu \
    && apt-get install -y php8.1-curl \
    && apt-get install -y php8.1-cli \
    && apt-get install -y php8.1-ldap \
    && apt-get install -y php8.1-pgsql \
    && apt-get install -y php8.1-gettext \
    && apt-get install -y php8.1-zip \
    && apt-get install -y php8.1-soap \
    && apt-get install -y php8.1-bcmath \
    && apt-get install -y php8.1-redis \
    && apt-get install -y php8.1-imap \
    && apt-get install -y php8.1-ssh2 \
    && apt-get install -y php8.1-pcov \
    && apt-get install -y php8.1-mongo \
    && apt-get install -y dh-php \
    #&& apt-get install -y php8.1-geoip \ not available for 8.1
    && apt-get install -y php8.1-amqp \
    # shopware required pcre
    && apt-get install -y libpcre3 libpcre3-dev \
&& cat /tmp/general.ini >| /etc/php/8.1/fpm/conf.d/01-general.ini \
&& cat /tmp/general.ini >| /etc/php/8.1/cli/conf.d/01-general.ini \
&& cat /tmp/cli.ini >| /etc/php/8.1/cli/conf.d/01-general-cli.ini \
    && apt-get install -y php8.0-fpm \
    && apt-get install -y php8.0-gd \
    && apt-get install -y php8.0-iconv \
    && apt-get install -y php8.0-intl \
    #&& apt-get install -y php8.0-json \
    && apt-get install -y php8.0-xml \
    && apt-get install -y php8.0-mbstring \
    && apt-get install -y php8.0-pdo \
    && apt-get install -y php8.0-mysql \
    && apt-get install -y php8.0-apc \
    && apt-get install -y php8.0-apcu \
    && apt-get install -y php8.0-curl \
    && apt-get install -y php8.0-cli \
    && apt-get install -y php8.0-ldap \
    && apt-get install -y php8.0-pgsql \
    && apt-get install -y php8.0-gettext \
    && apt-get install -y php8.0-zip \
    && apt-get install -y php8.0-soap \
    && apt-get install -y php8.0-bcmath \
    && apt-get install -y php8.0-redis \
    && apt-get install -y php8.0-imap \
    && apt-get install -y php8.0-ssh2 \
    && apt-get install -y php8.0-pcov \
    && apt-get install -y php8.0-mongo \
    && apt-get install -y dh-php \
#    && apt-get install -y php8.0-geoip \ not available for 8.0
    && apt-get install -y php8.0-amqp \
    # shopware required pcre
    && apt-get install -y libpcre3 libpcre3-dev \
&& cat /tmp/general.ini >| /etc/php/8.0/fpm/conf.d/01-general.ini \
&& cat /tmp/general.ini >| /etc/php/8.0/cli/conf.d/01-general.ini \
&& cat /tmp/cli.ini >| /etc/php/8.0/cli/conf.d/01-general-cli.ini \
    && apt-get install -y php7.4-fpm \
    && apt-get install -y php7.4-gd \
    #make sure mbstring is installed BEFORE iconv see: https://github.com/dockware/dockware/issues/36 => https://github.com/docker-library/php/issues/240#issuecomment-355489551
    && apt-get install -y php7.4-mbstring \
    && apt-get install -y php7.4-iconv \
    && apt-get install -y php7.4-intl \
    && apt-get install -y php7.4-json \
    && apt-get install -y php7.4-xml \
    && apt-get install -y php7.4-pdo \
    && apt-get install -y php7.4-mysql \
    && apt-get install -y php7.4-apcu \
    && apt-get install -y php7.4-apc \
    && apt-get install -y php7.4-curl \
    && apt-get install -y php7.4-cli \
    && apt-get install -y php7.4-ldap \
    && apt-get install -y php7.4-pgsql \
    && apt-get install -y php7.4-gettext \
    && apt-get install -y php7.4-zip \
    && apt-get install -y php7.4-soap \
    && apt-get install -y php7.4-bcmath \
    && apt-get install -y php7.4-redis \
    && apt-get install -y php7.4-imap \
    && apt-get install -y php7.4-ssh2 \
    && apt-get install -y php7.4-pcov \
    && apt-get install -y php7.4-mongo \
    && apt-get install -y dh-php \
    && apt-get install -y php7.4-geoip \
    && apt-get install -y php7.4-amqp \
    # shopware required pcre
    && apt-get install -y libpcre3 libpcre3-dev \
&& cat /tmp/general.ini >| /etc/php/7.4/fpm/conf.d/01-general.ini \
&& cat /tmp/general.ini >| /etc/php/7.4/cli/conf.d/01-general.ini \
&& cat /tmp/cli.ini >| /etc/php/7.4/cli/conf.d/01-general-cli.ini \

    && apt-get install -y php7.3-fpm \
    && apt-get install -y php7.3-apc \
    && apt-get install -y php7.3-apcu \
    && apt-get install -y php7.3-curl \
    && apt-get install -y php7.3-cli \
    && apt-get install -y php7.3-gd \
    && apt-get install -y php7.3-json \
    && apt-get install -y php7.3-ldap \
    && apt-get install -y php7.3-mysql \
    && apt-get install -y php7.3-pgsql \
    && apt-get install -y php7.3-gettext \
    && apt-get install -y php7.3-intl \
    && apt-get install -y php7.3-xml \
    && apt-get install -y php7.3-mysql \
    && apt-get install -y php7.3-mbstring \
    && apt-get install -y php7.3-zip \
    && apt-get install -y php7.3-soap \
    && apt-get install -y php7.3-memcached \
    && apt-get install -y php7.3-redis \
    && apt-get install -y php7.3-bcmath \
    && apt-get install -y php7.3-imap \
    && apt-get install -y php7.3-ssh2 \
    && apt-get install -y php7.3-pcov \
    && apt-get install -y dh-php \
    && apt-get install -y php7.3-geoip \
    && apt-get install -y php7.3-amqp \
    && apt-get install -y php7.3-mongo \

&& cat /tmp/general.ini >| /etc/php/7.3/fpm/conf.d/01-general.ini \
&& cat /tmp/general.ini >| /etc/php/7.3/cli/conf.d/01-general.ini \
&& cat /tmp/cli.ini >| /etc/php/7.3/cli/conf.d/01-general-cli.ini \

    && apt-get install -y php7.2-fpm \
    && apt-get install -y php7.2-apc \
    && apt-get install -y php7.2-apcu \
    && apt-get install -y php7.2-curl \
    && apt-get install -y php7.2-cli \
    && apt-get install -y php7.2-gd \
    && apt-get install -y php7.2-json \
    && apt-get install -y php7.2-ldap \
    && apt-get install -y php7.2-mysql \
    && apt-get install -y php7.2-pgsql \
    && apt-get install -y php7.2-gettext \
    && apt-get install -y php7.2-intl \
    && apt-get install -y php7.2-xml \
    && apt-get install -y php7.2-mysql \
    && apt-get install -y php7.2-mbstring \
    && apt-get install -y php7.2-zip \
    && apt-get install -y php7.2-soap \
    && apt-get install -y php7.2-memcached \
    && apt-get install -y php7.2-redis \
    && apt-get install -y php7.2-bcmath \
    && apt-get install -y php7.2-imap \
    && apt-get install -y php7.2-ssh2 \
    && apt-get install -y php7.2-pcov \
    && apt-get install -y dh-php \
    && apt-get install -y php7.2-geoip \
    && apt-get install -y php7.2-amqp \
    && apt-get install -y php7.2-mongo \

&& cat /tmp/general.ini >| /etc/php/7.2/fpm/conf.d/01-general.ini \
&& cat /tmp/general.ini >| /etc/php/7.2/cli/conf.d/01-general.ini \
&& cat /tmp/cli.ini >| /etc/php/7.2/cli/conf.d/01-general-cli.ini \
# remove pecl again
&& apt-get remove -y dh-php \
&& rm -rf /var/lib/apt/lists/* /var/cache/apt/*

#make sure the installation runs also in default php version
RUN sudo update-alternatives --set php /usr/bin/php7.4 > /dev/null 2>&1 &
# make sure the installation runs using our default php version
RUN service php7.4-fpm stop > /dev/null 2>&1 && \
    service php7.4-fpm start && \
    sudo update-alternatives --set php /usr/bin/php7.4 > /dev/null 2>&1 \

# make sure our php user has rights on the session
&& chown www-data:www-data -R /var/lib/php/sessions

# remove the standard apache index file
RUN mkdir -p /var/www/html \
    && rm -rf /var/www/html/* \
    && chown -R www-data:www-data /var/www/html \
    && sudo -u www-data sh -c 'mkdir -p /var/www/html/public'

# make sure the configured log folder exists and is writeable
RUN chmod -R 0777 /var/www \
    && chgrp -R www-data /var/log/apache2 \
    && mkdir -p /var/log/mysql \
    && chgrp -R www-data /var/log/mysql\
    && mkdir /var/log/php -p  \
    && touch /var/log/php/cli_errors.log  \
    && touch /var/log/php/fpm_errors.log  \
    && chown -R www-data:www-data /var/log/php  \
    && chmod 0755 /var/log/php

## ***********************************************************************
##  MOD_SSL
##  create SSL certificate
## ***********************************************************************
RUN apt-get update \
    && apt-get install -y openssl \
    && a2enmod ssl \
    && mkdir /etc/apache2/ssl \
    && openssl req -new -x509 -days 365 -sha1 -newkey rsa:2048 -nodes -keyout /etc/apache2/ssl/server.key -out /etc/apache2/ssl/server.crt -subj '/O=Company/OU=Department/CN=localhost' \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

RUN apt-get update \

    && apt-get install -y git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/tmp --version=2.2.9 --quiet \
    # prepare "global" composer directory for www-data
    && mkdir -p /var/www/.composer \
    && export COMPOSER_HOME="/var/www/.composer" \
    && chmod 755 /tmp/composer.phar \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/* \
    && mv /tmp/composer.phar /usr/local/bin/composer

## ***********************************************************************
##  MYSQL INSTALL
## ***********************************************************************

# prepare environment variables to allow a
# quiet install of the mysql server
# this sets the root password to root without user prompts
RUN echo debconf mysql-server/root_password password root | debconf-set-selections
RUN echo debconf mysql-server/root_password_again password root | debconf-set-selections

RUN DEBIAN_FRONTEND=noninteractive \
    # install mysql server
    && apt-get update \
    && apt-get install -y -q mysql-server \
    # set requires permissions
    && usermod -d /var/lib/mysql/ mysql \
    # cleanup download folders
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

# copy our custom configuration to the image
ADD ./config/mysql/my.cnf /etc/mysql/my.cnf

# i dont know why, but this is suddenly required
RUN chmod 0444 /etc/mysql/my.cnf \
    && service mysql start \
    && mysql --user=root --password=root -e "use mysql; update user set host='%' where host='localhost';" \
    && service mysql restart

## ***********************************************************************
##  ADMINER
## ***********************************************************************

RUN mkdir /usr/share/adminer \
    && wget "https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php" -O /usr/share/adminer/latest.php \
    && ln -s /usr/share/adminer/latest.php /usr/share/adminer/adminer.php \
    && echo "Alias /adminer.php /usr/share/adminer/adminer.php" | sudo tee /etc/apache2/conf-available/adminer.conf \
    && a2enconf adminer.conf

RUN apt-get update \
    && apt-get install -y build-essential \
    && apt-get install -y libsqlite3-dev \
    && apt-get install -y rubygems \
    && apt-get install -y ruby-dev \
    && gem install net-protocol -v 0.1.2 \
    && gem install net-smtp -v 0.3.0 \
    && gem install net-imap -v 0.2.2 \
    && gem install sqlite3 -v 1.3.4 \
    && gem install mailcatcher \
    && phpenmod mailcatcher \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

COPY ./config/ssmtp/ssmtp.conf /etc/ssmtp/ssmtp.conf
RUN echo "sendmail_path = /usr/bin/env $(which catchmail) -f 'local@dockware'" >> /etc/php/8.2/mods-available/mailcatcher.ini && \
echo "sendmail_path = /usr/bin/env $(which catchmail) -f 'local@dockware'" >> /etc/php/8.1/mods-available/mailcatcher.ini && \
echo "sendmail_path = /usr/bin/env $(which catchmail) -f 'local@dockware'" >> /etc/php/8.0/mods-available/mailcatcher.ini && \
echo "sendmail_path = /usr/bin/env $(which catchmail) -f 'local@dockware'" >> /etc/php/7.4/mods-available/mailcatcher.ini && \
echo "sendmail_path = /usr/bin/env $(which catchmail) -f 'local@dockware'" >> /etc/php/7.3/mods-available/mailcatcher.ini && \
echo "sendmail_path = /usr/bin/env $(which catchmail) -f 'local@dockware'" >> /etc/php/7.2/mods-available/mailcatcher.ini && \
    echo ""

RUN mkdir -p /var/www/pimpmylog && \
    wget -O - https://github.com/potsky/PimpMyLog/tarball/master | tar xzf - && \
    mv potsky-PimpMyLog-* /var/www/pimpmylog && \
    mv /var/www/pimpmylog/potsky-PimpMyLog-2fed8c1/* /var/www/pimpmylog && \
    rm -rf /var/www/pimpmylog/potsky-PimpMyLog-*

COPY /config/pimpmylog/config.user.d /var/www/pimpmylog/config.user.d
COPY /config/pimpmylog/config.user.json /var/www/pimpmylog/config.user.json

# apply our custom file with fixes for PHP 8
# its used from here: https://github.com/potsky/PimpMyLog/pull/149/files
COPY /config/pimpmylog/global.inc.php /var/www/pimpmylog/inc/global.inc.php

RUN chown -R www-data:www-data /var/www/pimpmylog/

RUN ls -la \
    && mkdir "/var/www/.nvm" \
    && export NVM_DIR="/var/www/.nvm" \
    # -----------------------------------------------------------------------------------------
    && curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash \
    # -----------------------------------------------------------------------------------------
    && [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" \
    && [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  \
    # -----------------------------------------------------------------------------------------
    && nvm install 20 \
    && nvm install 18 \
    && nvm install 16 \
    && nvm install 14 \
    && nvm install 12 \
    # -----------------------------------------------------------------------------------------
    # we have to install yarn in additional node versions
    # otherwise it won't be found after a nvm switch
    && nvm use 20 && npm install -g yarn \
    && nvm use 18 && npm install -g yarn \
    && nvm use 16 && npm install -g yarn \
    && nvm use 14 && npm install -g yarn \
    && nvm use 12 && npm install -g yarn \
    # -----------------------------------------------------------------------------------------
    && nvm use 12 \
    && nvm alias default 12  \
    # -----------------------------------------------------------------------------------------
    && echo ""

ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH      $NVM_DIR/v$NODE_VERSION/bin:$PATH

RUN echo "" \

    && rm -rf /var/lib/apt/lists/* /var/cache/apt/*

COPY ./assets/scripts/makefile /var/www/makefile
COPY ./assets/scripts/bin /var/www/scripts/bin
COPY ./assets/scripts/cron /var/www/scripts/cron

COPY ./assets/shopware6/files /var/www/scripts/shopware6

ADD entrypoint.sh /entrypoint.sh

RUN chown www-data:www-data -R /var/www/scripts && \
    #make sure for the whole installation xdebug is off for performance
    sh /var/www/scripts/bin/xdebug_disable.sh && \
    chmod 755 /*.sh

## ***********************************************************************
##  INSTALL SHOPWARE
## ***********************************************************************
RUN rm -rf /var/www/html/* \
    && wget --no-check-certificate https://releases.shopware.com/sw6/install_v6.3.5.0_ba08dbfc07784b5cefe7837f2abbda69dbf5b8b7.zip -qq -O /var/www/shopware.zip \
    && unzip -q /var/www/shopware.zip -d /var/www/html \
    && rm -rf /var/www/shopware.zip

RUN echo "APP_ENV=prod" >> /var/www/html/.env && \
    echo "APP_SECRET=1" >> /var/www/html/.env && \
    echo "INSTANCE_ID=1" >> /var/www/html/.env && \
    echo "DATABASE_URL=mysql://root:root@localhost:3306/shopware" >> /var/www/html/.env && \
    echo "APP_URL=http://localhost" >> /var/www/html/.env && \
    echo "MAILER_URL=smtp://localhost:1025" >> /var/www/html/.env && \
    echo "COMPOSER_HOME=/var/www/html/var/cache/composer" >> /var/www/html/.env && \
    echo "SHOPWARE_ES_ENABLED=0" >> /var/www/html/.env

RUN sudo service mysql start && \
    # switch to default PHP before installing
    sudo update-alternatives --set php /usr/bin/php7.4 > /dev/null 2>&1 && \
    # -------------------------------------------------------------------------------------------
    cd /var/www/html && php bin/console system:install --create-database --basic-setup && \
    # make sure assets like logos are ready
    cd /var/www/html && php bin/console assets:install && \
    rm -rf /var/www/html/config/jwt/private.pem && \
    rm -rf /var/www/html/config/jwt/public.pem && \
    cd /var/www/html && php bin/console system:generate-jwt-secret && \
    # -------------------------------------------------------------------------------------------
    # add some demo data
        cd /var/www/html && APP_ENV=prod php bin/console store:download -p SwagPlatformDemoData && \
    cd /var/www/html && APP_ENV=prod php bin/console plugin:refresh && \
    cd /var/www/html && APP_ENV=prod php bin/console plugin:install --activate SwagPlatformDemoData && \
        # -------------------------------------------------------------------------------------------
    # clear cache and refresh dal index to show the new demo data
    cd /var/www/html && php bin/console cache:clear && \
    cd /var/www/html && php bin/console dal:refresh:index && \
    rm -rf /var/www/html/var/cache/* && \
    # -------------------------------------------------------------------------------------------
    mysql --user=root --password=root -e "use shopware; INSERT INTO system_config (id, configuration_key, configuration_value, sales_channel_id, created_at, updated_at) VALUES (X'b3ae4d7111114377af9480c4a0911111', 'core.frw.completedAt', '{\"_value\": \"2019-10-07T10:46:23+00:00\"}', NULL, '2019-10-07 10:46:23.169', NULL);" && \
    sudo service mysql stop

RUN echo "" && \
    chown 33:33 -R /var/www/html && \
    mkdir -p /var/www/.npm && chown 33:33 /var/www/.npm -R && \
    # this is necessary so that our user can
    # change the default nvm node version
    # otherwise the persisted node version switch would not work!
    mkdir -p /var/www/.nvm && chown 33:33 /var/www/.nvm -R && \
    echo ""

## ***********************************************************************
## SWITCH TO NORMAL USER (NOT ROOT ANYMORE!)
## everything down here is now done as our www-data / dockware user
## just like you would do it manually in the container
## ***********************************************************************

USER dockware

# make the apache folder the working directory
WORKDIR /var/www/html

## ***********************************************************************
##  POST BUILD
## ***********************************************************************

ENTRYPOINT ["/bin/bash", "/entrypoint.sh"]
