.PHONY: help
.DEFAULT_GOAL := help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

status: ## status infos
	sh /var/www/scripts/bin/status.sh

xdebug-on: ## enables xdebug
	sh /var/www/scripts/bin/xdebug_enable.sh

xdebug-off: ## disables xdebug
	sh /var/www/scripts/bin/xdebug_disable.sh

permission-repair: ## repairs the general file permissions
	sudo chown -R www-data:www-data /var/www/html/*
	sudo chmod -R 775 /var/www/html
	cd /var/www/scripts/shopware6 && php create_jwt.php
	cd /var/www/html/config/jwt && sudo chown www-data:www-data *

# ---------------------------------------------------------------------------------------------
watch-storefront: ## starts watcher for storefront at http://localhost
	@echo "RequestHeader add hot-reload-mode 1\n"\
	"RequestHeader add hot-reload-port 9999" > /var/www/html/.htaccess.watch
	cd /var/www/html && sudo ./bin/build-storefront.sh
	cd /var/www/html && php bin/console theme:dump
	cd /var/www/html && sudo ./bin/watch-storefront.sh

stop-watch-storefront: ## Reverts everything back to normal operation
	@rm -rf /var/www/html/.htaccess.watch

# ---------------------------------------------------------------------------------------------

watch-admin: ## starts watcher for Shopware 6.4.3.1 Admin at http://localhost:8888
	cd /var/www/html && ./bin/build-administration.sh
	cd /var/www/html && php bin/console bundle:dump
	cd /var/www/html && php bin/console feature:dump
	cd /var/www/html && APP_URL=http://0.0.0.0 PROJECT_ROOT=/var/www/html APP_ENV=dev PORT=8888 HOST=0.0.0.0 ENV_FILE=/var/www/html/.env ./bin/watch-administration.sh

# ---------------------------------------------------------------------------------------------
build-admin: ## builds the admin
	cd /var/www/html && ./bin/build-administration.sh

