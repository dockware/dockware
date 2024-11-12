.PHONY: help
.DEFAULT_GOAL := help

PHP_VERSION_CURRENT=$(shell php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
XDEBUG_STATUS=$(shell php -r 'echo (int)extension_loaded("xdebug");')

help:
	@echo "COMMANDS"
	@echo "---------------------------------------------------------------------------------------------------------"
	@printf "\033[33mInstallation:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##1 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##1 "}; {printf "\033[33m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "---------------------------------------------------------------------------------------------------------"
	@printf "\033[36mDev:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##2 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##2 "}; {printf "\033[36m  - %-30s\033[0m %s\n", $$1, $$2}'
	@echo "---------------------------------------------------------------------------------------------------------"
	@printf "\033[35mDevOps:%-30s\033[0m %s\n"
	@grep -E '^[a-zA-Z_-]+:.*?##3 .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*?##3 "}; {printf "\033[35m  - %-30s\033[0m %s\n", $$1, $$2}'

status: ##3 status infos
	sh /var/www/scripts/bin/status.sh

xdebug-on: ##2 enables xdebug
	sh /var/www/scripts/bin/xdebug_enable.sh

xdebug-off: ##2 disables xdebug
	sh /var/www/scripts/bin/xdebug_disable.sh

permission-repair: ##3 repairs the general file permissions
	sudo chown -R www-data:www-data /var/www/html/*
	sudo chmod -R 775 /var/www/html
	cd /var/www/scripts/shopware6 && php create_jwt.php
	cd /var/www/html/config/jwt && sudo chown www-data:www-data *

restart-php: ##3 Restarts FPM and Apache2
	sudo service php$(PHP_VERSION_CURRENT)-fpm stop
	sudo service php$(PHP_VERSION_CURRENT)-fpm start
	@# ------------------------------------------------------
	sudo service apache2 restart

switch-php: ##3 Switches to another supported PHP version, [make switch-php version=7.4]
ifndef version
	$(warning Provide the required PHP version using "make switch-php version=x.y")
	@exit 1;
else
	@# ATTENTION, keep this steps exactly like they are. they ensure that its fail-fast
	@# non existing PHP versions will do nothing, switching to the same php version will also work
	@# ---------------------------------------------------------------------------------------
	@# Start new PHP to see if its existing, then stop current one, and if we use the same php version that
	@# is currently used, just make sure to start it again ;)
	sudo service php$(version)-fpm start
	sudo service php$(PHP_VERSION_CURRENT)-fpm stop
	sudo service php$(version)-fpm start
	export PHP_VERSION=$(version)
	@# ---------------------------------------------------------------------------------------
	@# if that all worked, switch the apache config now...that avoids breaking the files
	@# pipe the first command to dev/null to avoid ugly warnings in entrypoint boot script if old placeholder does not exist anymore
	sudo sed -i 's/__dockware_php_version__/'$(version)'/g' /etc/apache2/sites-enabled/000-default.conf 2>/dev/null
	sudo sed -i 's/'php$(PHP_VERSION_CURRENT)-fpm.sock'/'php$(version)-fpm.sock'/g' /etc/apache2/sites-enabled/000-default.conf
	sudo service apache2 restart
	@# ---------------------------------------------------------------------------------------
	@# last but not least update php for cli -> this is used for our variable in here
	sudo update-alternatives --set php /usr/bin/php$(version)
ifeq ($(XDEBUG_STATUS),1)
	make xdebug-on
else
	make xdebug-off
endif
	php -v
endif

# ---------------------------------------------------------------------------------------------
watch-storefront: ##2 starts watcher for storefront at http://localhost
	@echo "RequestHeader add hot-reload-mode 1\n"\
	"RequestHeader add hot-reload-port 9999" > /var/www/html/.htaccess.watch
	cd /var/www/html && ./bin/build-storefront.sh
	cd /var/www/html && php bin/console theme:dump
	cd /var/www/html && ./bin/watch-storefront.sh

stop-watch-storefront: ## Reverts everything back to normal operation
	@rm -rf /var/www/html/.htaccess.watch

# ---------------------------------------------------------------------------------------------

watch-admin: ##2 starts watcher for Shopware 6.6.8.1 Admin at http://localhost:8888
	cd /var/www/html && ./bin/build-administration.sh
	cd /var/www/html && php bin/console bundle:dump
	cd /var/www/html && php bin/console feature:dump
	cd /var/www/html && APP_URL=http://0.0.0.0 PROJECT_ROOT=/var/www/html APP_ENV=dev PORT=8888 HOST=0.0.0.0 ENV_FILE=/var/www/html/.env ./bin/watch-administration.sh

# ---------------------------------------------------------------------------------------------
build-admin: ##2 builds the admin
	cd /var/www/html && ./bin/build-administration.sh

