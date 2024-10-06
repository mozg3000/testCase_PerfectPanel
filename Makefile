init: pull build dirs permission install
pull:
	docker-compose pull
build:
	docker-compose build
up:
	docker-compose up -d
down:
	docker-compose down --remove-orphans
restart:
	docker-compose restart
composer-init:
	docker-compose run --rm php-cli composer init
require:
	docker-compose run --rm php-cli composer require $(NAME)
install:
	docker-compose run --rm php-cli composer install
dirs:
	docker-compose run --rm php-cli mkdir runtime/logs
permission:
	docker-compose run --rm php-cli chmod -R 777 web runtime views models controllers migrations