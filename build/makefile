#
# Makefile
#

.PHONY: help build
.DEFAULT_GOAL := help


help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# ----------------------------------------------------------------------------------------------------------------

generate-actions: ## Generates the github actions files for releasing dockware
	php build.php

generate-dist:## Generates the current docker files and commits it
ifndef version
	$(warning Provide the required version name using "make commit-dist version=master")
	@exit 1;
else
	@cd .. && make generate
	@cd .. && rm -rf ./.dist/versions/$(version)
	cd .. && mkdir -p ./.dist/versions/$(version)
	cd .. && cp -r ./dist/images/* ./.dist/versions/$(version)/
endif

generate-tests: ## Generates the SVRUnit Tests
	php build-tests.php

docker-generate-tests: ## Generates the SVRUnit Tests in Docker, if you have no PHP
	docker run --rm -v ${PWD}/../:/var/www/html dockware/flex:latest bash -c 'cd build && make generate-tests'