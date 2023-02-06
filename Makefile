debug-router:
	php bin/console debug:router
rector:
	php vendor/bin/rector process --clear-cache
d:
	php bin/console doctrine
dm:
	php bin/console doctrine:migrations:migrate --no-interaction -vv
dm-test:
	php bin/console --env=test doctrine:migrations:migrate --no-interaction -vv
dm-prev:
	php bin/console doctrine:migrations:migrate prev --no-interaction --allow-no-migration
dm-next:
	php bin/console doctrine:migrations:migrate next --no-interaction --allow-no-migration
dd:
	php bin/console doctrine:migrations:diff
	chmod -R 777 migrations
dg:
	php bin/console doctrine:migrations:generate
	chmod -R 777 migrations
rm:
	rm -rf ../vendor
	rm -rf vendor/*
	rm -f composer.lock
	rm -f symfony.lock
ca:
	composer dump-autoload --optimize
ci:
	composer self-update
	composer clearcache
	composer i
	#composer update
chmod:
	chmod -R 777 vendor
	chmod -R 777 var
	chmod -R 777 src
	chmod -R 777 public
wf-category:
	php bin/console workface:category:tree:checker
i: ci chmod dm cache-clear wf-category
i-first: rm i
me-up:
	 php bin/console doctrine:migrations:execute --up 'DoctrineMigrations\\'$$name --no-interaction
me-down:
	 php bin/console doctrine:migrations:execute --down 'DoctrineMigrations\\'$$name --no-interaction
mm:
	php bin/console make:migration
mv-add:
	php bin/console doctrine:migrations:version $$name --add --no-interaction
mv-add-all:
	php bin/console doctrine:migrations:version --add --all --no-interaction
cache-clear:
	bin/console doctrine:cache:clear-metadata
	bin/console doctrine:cache:clear-query
	bin/console doctrine:cache:clear-result
	chmod -R 777 ./var
rd:
	bin/console debug:router $$name
php-phpunit:
	php ./vendor/bin/phpunit
db-create:
	php bin/console doctrine:database:create --env=test --if-not-exists
schema-create:
	php bin/console doctrine:schema:create --env=test
test-first: db-create schema-create dm php-phpunit
test: php-phpunit
schema-drop:
	php bin/console --env=test doctrine:schema:drop --force --dump-sql
fixtures-create:
	php bin/console make:fixtures
jwt-gen-keys:
	mkdir -p config/jwt
	openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
	openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout