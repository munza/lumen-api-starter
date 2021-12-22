PROJECT_NAME="Lumen API Starter"
IMAGE_NAME="lumen-api-starter-app"

.PHONY: help

help:
	@echo "\n\033[1;32m${PROJECT_NAME}\033[0m\n"
	@echo "\033[4mCommands:\033[0m\n"
	@echo "make [command]\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' ${MAKEFILE_LIST} | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-10s\033[0m %s\n", $$1, $$2}'

build: ## Build all Docker images
	@docker build . --file Dockerfile --tag ${IMAGE_NAME}:latest

up: ## Start all Docker services
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

dev-test: ## Run test withing Docker-Compose
	@docker-compose run --rm app vendor/bin/phpunit

install: ## Install Composeer dependencies
	@docker run --rm -v $(PWD):/app composer:2 composer install

test: build ## Run PHPUnit tests
	@docker run --rm ${IMAGE_NAME}:latest vendor/bin/phpunit
