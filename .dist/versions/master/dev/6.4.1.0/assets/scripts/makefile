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

# ---------------------------------------------------------------------------------------------
watch-storefront: ## starts watcher for storefront at http://localhost
	@echo "RequestHeader add hot-reload-mode 1\n"\
	"RequestHeader add hot-reload-port 9999" > /var/www/html/.htaccess.watch
	cd /var/www/html && ./bin/build-storefront.sh
	cd /var/www/html && php bin/console theme:dump
	cd /var/www/html && ./bin/watch-storefront.sh

stop-watch-storefront: ## Reverts everything back to normal operation
	@rm -rf /var/www/html/.htaccess.watch

# ---------------------------------------------------------------------------------------------

watch-admin: ## starts watcher for admin at http://localhost:8888
	cd /var/www/html && ./bin/build-administration.sh
	cd /var/www/html && php bin/console bundle:dump
	cd /var/www/html && php bin/console feature:dump
	cd /var/www/html && APP_URL=http://0.0.0.0 PROJECT_ROOT=/var/www/html APP_ENV=dev PORT=8888 HOST=0.0.0.0 ENV_FILE=/var/www/html/.env ESLINT_DISABLE=true npm run --prefix /var/www/html/vendor/shopware/administration/Resources/app/administration dev
	
# ---------------------------------------------------------------------------------------------
build-admin: ## builds the admin
	cd /var/www/html && ./bin/build-administration.sh

