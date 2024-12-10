.PHONY: init build-database up down unit-tests

init:
	cd backend && composer install && cd .. && docker-compose up --build

build-database:
	docker exec -i $$(docker-compose ps -q db) mysql -uuser -ppassword ehealth < schema.sql

up:
	docker-compose up -d

down:
	docker-compose down

unit-tests:
	./vendor/phpunit/phpunit/phpunit tests