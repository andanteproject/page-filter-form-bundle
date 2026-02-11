.PHONY: setup setup-php74 setup-php85 php php74 php85 cs-fixer cs-fixer-php74 cs-fixer-php85 phpstan phpstan-php74 phpstan-php85 tests tests-php74 tests-php85

setup: setup-php74 setup-php85

setup-php74:
	rm -f composer.lock
	docker-compose up --build -d php74
	docker-compose exec php74 composer install

setup-php85:
	rm -f composer.lock
	docker-compose up --build -d php85
	docker-compose exec php85 sh -c "COMPOSER_VENDOR_DIR=vendor85 composer install"

php: php74

php74:
	docker-compose exec php74 sh

php85:
	docker-compose exec php85 sh

# Run CS Fixer on PHP 7.4 only so fixes stay compatible with minimum supported PHP version
cs-fixer: cs-fixer-php74

cs-fixer-php74:
	docker-compose exec php74 vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --allow-risky=yes

cs-fixer-php85:
	docker-compose exec php85 vendor85/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --allow-risky=yes

phpstan: phpstan-php74 phpstan-php85

phpstan-php74:
	docker-compose exec php74 vendor/bin/phpstan analyse src tests --configuration=phpstan.neon --memory-limit=1G

phpstan-php85:
	docker-compose exec php85 sh -c "mv vendor vendor_tmp && COMPOSER_VENDOR_DIR=vendor85 vendor85/bin/phpstan analyse src tests --configuration=phpstan.neon --memory-limit=1G; mv vendor_tmp vendor"

tests: tests-php74 tests-php85

tests-php74:
	rm -rf var/cache/test
	mkdir -p var/cache/test
	docker-compose exec php74 vendor/bin/phpunit

tests-php85:
	rm -rf var/cache/test
	mkdir -p var/cache/test
	docker-compose exec php85 sh -c "COMPOSER_VENDOR_DIR=vendor85 vendor85/bin/phpunit"

ci-local:
	act -j build
