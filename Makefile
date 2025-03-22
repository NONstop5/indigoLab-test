install:
	composer install

validate:
	composer validate

start:
	symfony server:start
	#php -S localhost:8000 -t public/

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

.PHONY: tests
