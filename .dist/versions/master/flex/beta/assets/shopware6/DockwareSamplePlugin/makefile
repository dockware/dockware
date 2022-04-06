#
# Makefile
#

.PHONY: help
.DEFAULT_GOAL := help

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# ------------------------------------------------------------------------------------------------------------

prod: ## Installs all production dependencies
	composer install --no-dev

dev: ## Installs all dev dependencies
	composer install

test: ## Starts all Tests
	php ./vendor/bin/phpunit --configuration=./phpunit.xml

stan: ## Starts the PHPStan Analyser
	php ./vendor/bin/phpstan --memory-limit=1G analyse .