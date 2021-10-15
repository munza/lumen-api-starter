.PHONY: help

help:
	@echo "\nLumen API Starter - Make\n"
	@echo "\033[4mCommands:\033[0m\n"
	@echo "make [command]\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-10s\033[0m %s\n", $$1, $$2}'

up: ## Up all Docker services
	@docker-compose up -d

stop: ## Stop all Docker services
	@docker-compose stop

down: ## Down all Docker services
	@docker-compose down

logs: ## Follow logs from Docker service app
	@docker-compose logs -f app

ssh: ## SSH into Docker service app
	@docker-compose run --rm app sh

composer: ## SSH into a Composer container
	@docker run --rm -it -v $(PWD):/app composer:2 sh

test: ## Run PHPUnit tests
	@docker-compose run --rm app vendor/bin/phpunit
