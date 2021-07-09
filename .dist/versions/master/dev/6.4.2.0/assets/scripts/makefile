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
	cd /var/www/html && php bin/console theme:dump
	cd /var/www/html && php psh.phar storefront:hot-proxy

stop-watch-storefront: ## Reverts everything back to normal operation
	@rm -rf /var/www/html/.htaccess.watch

# ---------------------------------------------------------------------------------------------

watch-admin: ## starts watcher for admin at http://localhost:8888
	cd /var/www/html && PROJECT_ROOT=/var/www/html APP_URL=http://localhost ESLINT_DISABLE=true PORT=8888 HOST=0.0.0.0 ENV_FILE=/var/www/html/.env npm run --prefix vendor/shopware/platform/src/Administration/Resources/app/administration/ dev

# ---------------------------------------------------------------------------------------------
pull: ## pulls the latest github version and installs everything again
	cd /var/www/html && git pull
	cd /var/www/html/platform && git pull
	cd /var/www/html && ./psh.phar install

