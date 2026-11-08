# Developer shortcuts. Everything runs inside Docker, so only Docker is required on the host.
# `make help` lists targets.

.DEFAULT_GOAL := help
export UID := $(shell id -u)
export GID := $(shell id -g)

DC  := docker compose
RUN := $(DC) run --rm --no-deps -e CLAUDECODE -e AI_AGENT app
EXEC := $(DC) exec -e CLAUDECODE -e AI_AGENT app

# Laravel Cloud CLI. It is itself a Laravel app (it registers App\\), so it must NOT be a project
# dependency: it lives in ~/.config/cloud/cli on the host, next to the API token it stores.
CLOUD_DIR := $(HOME)/.config/cloud
CLOUD_RUN := $(DC) run --rm --no-deps -v $(CLOUD_DIR):/home/app/.config/cloud -e COMPOSER_HOME=/home/app/.config/cloud/cli
CLOUD_BIN := /home/app/.config/cloud/cli/vendor/bin/cloud

.PHONY: help setup up down restart logs shell fresh storage-link test coverage lint lint-fix analyse rector rector-fix ci build-prod cloud-install cloud-login cloud

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

setup: ## First run: build images, install deps, create .env, migrate + seed
	$(DC) build
	@test -f .env || cp .env.example .env
	$(RUN) composer install
	$(RUN) php artisan key:generate --no-interaction
	$(RUN) npm install
	$(DC) up -d pgsql redis
	$(DC) run --rm app php artisan migrate --seed --force
	$(RUN) php artisan storage:link --relative

up: ## Start the stack (app :8000, vite :5173, mailpit :8025)
	$(DC) up -d
	@echo "→ http://localhost:8000   admin: http://localhost:8000/admin   mail: http://localhost:8025"

down: ## Stop the stack
	$(DC) down

restart: down up ## Restart the stack

logs: ## Tail logs
	$(DC) logs -f app queue

shell: ## Shell inside the app container
	$(EXEC) bash

fresh: ## Drop and re-seed the database
	$(EXEC) php artisan migrate:fresh --seed

storage-link: ## Link public/storage → storage/app/public (fixes 403 on uploaded images)
	$(RUN) php artisan storage:link --relative --force
	@ls -l public/storage

test: ## Run the Pest suite (arch + unit + feature)
	$(RUN) composer test

coverage: ## Run Pest with coverage, failing below 100%
	$(RUN) composer test:coverage

lint: ## Check code style (Pint)
	$(RUN) composer lint

lint-fix: ## Fix code style (Pint)
	$(RUN) composer lint:fix

analyse: ## Static analysis (Larastan, max level)
	$(RUN) composer analyse

rector: ## Show Rector refactors (dry run)
	$(RUN) composer refactor

rector-fix: ## Apply Rector refactors
	$(RUN) composer refactor:fix

ci: ## Run the same checks as CI
	$(RUN) composer ci

build-prod: ## Build the production image locally
	docker build -f docker/Dockerfile --target prod -t portfolio:local .

cloud-install: ## Install/update the Laravel Cloud CLI (outside the project, in ~/.config/cloud/cli)
	@mkdir -p $(CLOUD_DIR)
	$(CLOUD_RUN) app composer global require laravel/cloud-cli --no-interaction --quiet

cloud-login: ## Save a Laravel Cloud API token (Cloud → Organization settings → API tokens)
	@test -x $(CLOUD_DIR)/cli/vendor/bin/cloud || $(MAKE) cloud-install
	@printf 'Laravel Cloud API token: '; stty -echo; read -r T; stty echo; echo; \
	  printf '%s' "$$T" | $(CLOUD_RUN) -T app $(CLOUD_BIN) auth:token --add -n

cloud: ## Run a Cloud CLI command, e.g. make cloud ARGS="application:list --json -n"
	@test -x $(CLOUD_DIR)/cli/vendor/bin/cloud || $(MAKE) cloud-install
	@$(CLOUD_RUN) app $(CLOUD_BIN) $(ARGS)
