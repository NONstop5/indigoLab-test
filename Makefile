install:
	composer install

validate:
	composer validate

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
	docker-compose --env-file ./docker/.env up -d

docker-rebuild:
	docker-compose --env-file ./docker/.env build --no-cache
	docker-compose --env-file ./docker/.env up -d

docker-config:
	docker-compose --env-file ./docker/.env config

shell:
	docker-compose exec php bash

.PHONY: tests
