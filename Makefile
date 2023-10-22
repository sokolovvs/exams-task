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
	php bin/console graphql:validate
test_env:
	php bin/console doctrine:database:drop --env=test --if-exists --force
	php bin/console doctrine:database:create --env=test --if-not-exists
	php bin/console doctrine:migrations:migrate --env=test --no-interaction
fixtures:
	php bin/console doctrine:fixtures:load