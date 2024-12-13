.PHONY: init deploy-database up down unit-tests phpstan

init:
	composer install && docker-compose build

deploy-database:
	docker exec -i $$(docker-compose ps -q db) mysql -uuser -ppassword ehealth < schema.sql

up:
	docker-compose up -d --remove-orphans

down:
	docker-compose down

unit-tests:
	./vendor/phpunit/phpunit/phpunit tests

phpstan:
	./vendor/bin/phpstan analyse src --level=$(level)