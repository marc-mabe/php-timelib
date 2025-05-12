SHELL := /bin/bash
PROJECT := php-timelib

.PHONY: help
help:
	@echo "# Available targets:"
	@grep -E '^[a-zA-Z][a-zA-Z0-9.-]+[[:space:]]*:([^=]|$$)' Makefile | sed -e 's/:$$//'
	@echo "# For a complete list with description run: $(MAKE) -p"

.PHONY: build-php-8.4
build-php-8.4:
	docker build -f ./Dockerfile -t '$(PROJECT):php-8.4' --build-arg 'PHP_VERSION=8.4' .

.PHONY: composer-install-php-8.4
composer-install-php-8.4:
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' composer install

.PHONY: composer-update-php-8.4
composer-update-php-8.4:
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' composer update

.PHONY: test-php-8.4
test-php-8.4:
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' php ./vendor/bin/phpunit ./tests

.PHONY: test-phpstan
test-phpstan:
	docker run --rm -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' php ./vendor/bin/phpstan analyse

test: test-php-8.4 test-phpstan

.PHONY: shell-php-8.4
shell-php-8.4:
	docker run --rm -it -u "$$(id -u):$$(id -g)" -v "$$(pwd):/workdir" '$(PROJECT):php-8.4' sh
