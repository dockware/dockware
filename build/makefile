#
# Makefile
#

.PHONY: help build
.DEFAULT_GOAL := help


help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# ----------------------------------------------------------------------------------------------------------------

generate-actions: ## Generates the github actions files for releasing dockware
	php generate-build-actions.php dev
	php generate-build-actions.php play
	php generate-build-actions.php contribute
	php generate-build-actions.php flex
	php generate-build-actions.php essentials

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
