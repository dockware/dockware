#
# Makefile
#

.PHONY: help build
.DEFAULT_GOAL := help


help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

# ----------------------------------------------------------------------------------------------------------------

generate-actions: ## Generates the github actions files for relaesing dockware
	php generate-githubaction-build-dev.php dev
	php generate-githubaction-build-dev.php play
	php generate-githubaction-build-dev.php contribute
	php generate-githubaction-build-dev.php flex
	php generate-githubaction-build-dev.php essentials
