install:
	docker-compose build
	docker-compose up -d
	docker-compose run --rm sf_app composer install
	docker-compose run --rm sf_app php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
test:
	make validate
	make test_env
	php bin/phpunit
coverage:
	make validate
	make test_env
	php -d memory_limit=1G -d xdebug.mode=coverage bin/phpunit --coverage-html=".codecoverage" --path-coverage
validate:
	php bin/console lint:container
test_env:
	php bin/console doctrine:database:drop --env=test --if-exists --force
	php bin/console doctrine:database:create --env=test --if-not-exists
	php bin/console doctrine:migrations:migrate --env=test --no-interaction
fixtures:
	php bin/console doctrine:fixtures:load --no-interaction

cs-fix:
	php ./vendor/bin/php-cs-fixer fix ./src
cs-diff:
	 php ./vendor/bin/php-cs-fixer fix ./src --dry-run --diff
psalm:
	php ./vendor/bin/psalm
psalm-full:
	php ./vendor/bin/psalm --show-info=true