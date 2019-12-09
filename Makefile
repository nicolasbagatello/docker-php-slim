# Makefile for Docker Nginx PHP Composer MySQL

include .env

# MySQL
MYSQL_DUMPS_DIR=data/db/dumps

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  code-sniff-check          Check the API with PHP Code Sniffer (PSR2)"
	@echo "  code-sniff-fix            Fix PSR2 using PHP Code Sniffer"
	@echo "  clean                     Clean directories for reset"
	@echo "  composer-up               Update PHP dependencies with composer"
	@echo "  create-migration          Create new migrations -> sudo make create-migration NAME='MyNewMigration'"
	@echo "  create-seed               Create new seed -> sudo make create-seed NAME='MyNewSeed'"
	@echo "  docker-start              Create and start containers"
	@echo "  docker-stop               Stop and clear all services"
	@echo "  logs                      Follow log output"
	@echo "  mysql-dump                Create backup of all databases"
	@echo "  mysql-restore             Restore backup of all databases"
	@echo "  test                      Test application"
	@echo "  rollback-all-migrations   Rollback all migrations"
	@echo "  rollback                  Run all seeds -> sudo make rollback DATE='2019'"
	@echo "  run-all-migrations        Run all migrations"
	@echo "  run-all-seeds             Run all seeds"


init:
	@$(shell cp -n $(shell pwd)/web/app/composer.json.dist $(shell pwd)/web/app/composer.json 2> /dev/null)

clean:
	@rm -Rf data/db/mysql/*
	@rm -Rf $(MYSQL_DUMPS_DIR)/*
	@rm -Rf web/app/vendor
	@rm -Rf web/app/composer.lock
	@rm -Rf web/app/doc
	@rm -Rf web/app/report
	@rm -Rf etc/ssl/*

code-sniff-check:
	@echo "Checking the standard code..."
	@docker-compose exec -T php ./app/vendor/bin/phpcs -v --standard=PSR2 app/src
	@docker-compose exec -T php ./app/vendor/bin/phpcs -v --standard=PSR2 public

code-sniff-fix:
	@echo "Checking the standard code..."
	@docker-compose exec -T php ./app/vendor/bin/phpcbf -v --standard=PSR2 app/src
	@docker-compose exec -T php ./app/vendor/bin/phpcbf -v --standard=PSR2 public

composer-up:
	@docker run --rm -v $(shell pwd)/web/app:/app composer update

create-migration:
	@docker-compose exec -T php ./app/vendor/bin/phinx create ${NAME}
	@make resetOwner

create-seed:
	@docker-compose exec -T php ./app/vendor/bin/phinx seed:create ${NAME}
	@make resetOwner

docker-start: init
	docker-compose up -d

docker-stop:
	@docker-compose down -v
	@make clean

logs:
	@docker-compose logs -f

mysql-dump:
	@mkdir -p $(MYSQL_DUMPS_DIR)
	@docker exec $(shell docker-compose ps -q mysqldb) mysqldump --all-databases -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" > $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null
	@make resetOwner

mysql-restore:
	@docker exec -i $(shell docker-compose ps -q mysqldb) mysql -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" < $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null

run-all-migrations:
	@docker-compose exec -T php ./app/vendor/bin/phinx migrate
	@make resetOwner

run-all-seeds:
	@docker-compose exec -T php ./app/vendor/bin/phinx seed:run
	@make resetOwner

test: code-sniff
	@docker-compose exec -T php ./app/vendor/bin/phpunit --colors=always --configuration ./app/
	@make resetOwner

rollback-all-migrations:
	@docker-compose exec -T php ./app/vendor/bin/phinx rollback
	@make resetOwner

rollback:
	@docker-compose exec -T php ./app/vendor/bin/phinx rollback -t ${DATE}
	@make resetOwner

resetOwner:
	@$(shell chown -Rf $(SUDO_USER):$(shell id -g -n $(SUDO_USER)) $(MYSQL_DUMPS_DIR) "$(shell pwd)/etc/ssl" "$(shell pwd)/web/app" 2> /dev/null)

.PHONY: clean test code-sniff init