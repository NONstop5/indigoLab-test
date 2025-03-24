install:
	composer install

validate:
	composer validate

PORT ?= 8000
start:
	symfony server:start
	#PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

lint: phpcsfixer-check phpstan

lint-fix: phpcsfixer-fix phpstan

phpcsfixer-check:
	vendor/bin/php-cs-fixer fix --diff --dry-run

phpcsfixer-fix:
	vendor/bin/php-cs-fixer fix

phpstan:
	vendor/bin/phpstan analyse

phpstan-baseline:
	vendor/bin/phpstan analyse src tests --generate-baseline

test:
	./bin/phpunit

docker-up-d:
	docker-compose --env-file ./docker/.env up -d

docker-down:
	docker-compose --env-file ./docker/.env down -v

docker-build:
	docker-compose --env-file ./docker/.env build

.PHONY: tests
