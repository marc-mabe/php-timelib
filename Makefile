SHELL := /bin/bash
PROJECT := php-timelib

.PHONY: help
help:
	@echo "# Available targets:"
	@grep -E '^[a-zA-Z][a-zA-Z0-9.-]+[[:space:]]*:([^=]|$$)' Makefile | sed -e 's/:$$//'
	@echo "# For a complete list with description run: $(MAKE) -p"

.PHONY: build-php-8.4
build-php-8.4:
	docker build --pull -f ./Dockerfile -t '$(PROJECT):php-8.4' --build-arg 'PHP_VERSION=8.4' .

.PHONY: build-php-8.4-x64
build-php-8.4-x64:
	docker build --pull --platform=amd64 -f ./Dockerfile -t '$(PROJECT):php-8.4' --build-arg 'PHP_VERSION=8.4' .

.PHONY: build-php-8.4-x32
build-php-8.4-x32:
	docker build --pull --platform=i386 -f ./Dockerfile -t '$(PROJECT):php-8.4' --build-arg 'PHP_VERSION=8.4' .

.PHONY: composer-install-php-8.4
composer-install-php-8.4:
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' composer install

.PHONY: composer-update-php-8.4
composer-update-php-8.4:
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' composer update

.PHONY: test-php-8.4
test-php-8.4:
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' /bin/sh -c '$$RUN_TESTS_BIN --no-progress --offline --show-diff ./tests'

.PHONY: test-phpstan
test-phpstan:
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' php -d memory_limit=2g ./vendor/bin/phpstan analyse

test: test-php-8.4 test-phpstan

.PHONY: shell-php-8.4
shell-php-8.4:
	docker run --rm -it -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' sh
