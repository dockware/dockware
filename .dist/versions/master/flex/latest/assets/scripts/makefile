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
	chown -R www-data:www-data /var/www/html/*
	chmod -R 775 /var/www/html
	cd /var/www/scripts/shopware6 && php create_jwt.php
	cd /var/www/html/config/jwt && chown www-data:www-data *

# ---------------------------------------------------------------------------------------------

