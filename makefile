#
# Makefile
#

.PHONY: help build
.DEFAULT_GOAL := help


help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# ----------------------------------------------------------------------------------------------------------------

install: ## Installs all dependencies
	curl -O https://orca-build.io/downloads/orca.zip
	unzip -o orca.zip
	rm -f orca.zip
	curl -O https://www.svrunit.com/downloads/svrunit.zip
	unzip -o svrunit.zip
	rm -f svrunit.zip

generate: ## Generates all artifacts for this image
	@php orca.phar --directory=.

clear: ## Clears all dangling images
	docker images -aq -f 'dangling=true' | xargs docker rmi
	docker volume ls -q -f 'dangling=true' | xargs docker volume rm

build: ## Builds the provided tag [image=play tag=6.1.6]
ifndef tag
	$(warning Provide the required image tag using "make build image=play tag=6.1.6")
	@exit 1;
else
	@cd ./dist/images/$(image)/$(tag) && docker build -t dockware/$(image):$(tag) .
endif

test: ## Runs all SVRUnit Test Suites or the provided image, image=xyz
ifndef image
	@php svrunit.phar --configuration=./tests/svrunit/flex.xml
	@php svrunit.phar --configuration=./tests/svrunit/essentials.xml
	@php svrunit.phar --configuration=./tests/svrunit/play.xml
	@php svrunit.phar --configuration=./tests/svrunit/dev.xml
	@php svrunit.phar --configuration=./tests/svrunit/contribute.xml
else
	@php svrunit.phar --configuration=./tests/svrunit/$(image).xml
endif
