# HELP
# This will output the help for each task
# thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
.PHONY: help

help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help

THIS_FILE := $(lastword $(MAKEFILE_LIST))

# DOCKER TASKS

# Build the container
build: ## Build the release and develoment container. The development
	@if [ "$$(docker images -q wiryonolau/php:5.6-cli)" = "" ]; then \
		cd ./docker && docker build -t wiryonolau/php:5.6-cli .; \
	else \
		echo "Image exist"; \
	fi

clean:
	@$(MAKE) -f $(THIS_FILE) stop
	@while [ -z "$$CONTINUE" ]; do \
		read -r -p "Remove wiryonolau/php:5.6-cli image ?. [y/N] " CONTINUE; \
	done ; \
    if [ $$CONTINUE = "y" ] || [ $$CONTINUE = "Y" ]; then \
		if [ ! "$$(docker images -q wiryonolau/php:5.6-cli)" = "" ]; then \
        	echo "Removing image"; \
			docker rmi wiryonolau/php:5.6-cli; \
		fi \
    fi


# Start the container
start:
	@$(MAKE) -f $(THIS_FILE) stop
	@$(MAKE) -f $(THIS_FILE) build
	docker run -d --rm -it -v $$(pwd):/srv/php-config-db -w /srv/php-config-db -e LOCAL_USER_ID=1000 -e COMPOSER_HOME=/srv/php-config-db/.composer -e USER_ID=1000 --name php-config-db wiryonolau/php:5.6-cli
	sleep 1
	docker exec php-config-db gosu 1000 git config --global http.sslverify false
	docker exec php-config-db gosu 1000 composer self-update
	docker exec php-config-db gosu 1000 composer install --no-plugins --no-scripts --no-dev --prefer-dist -v

stop:
	@if [ ! "$$(docker ps -aq -f name=php-config-db)" = "" ]; then \
		docker container stop php-config-db; \
	fi
	@if [ ! "$$(docker ps -aq -f name=php-config-db)" = "" ]; then \
		docker container rm php-config-db; \
	fi

