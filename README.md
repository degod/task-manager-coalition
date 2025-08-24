# Task Management Program (Laravel)

A simple Laravel web application for managing tasks.

## Features

-   Create task (stores: task name, priority, timestamps)
-   Edit task
-   Delete task
-   Reorder tasks with drag-and-drop in the browser
    -   Priority updates automatically based on position (top = priority 1, next = 2, etc.)
-   Persist tasks in a MySQL database
-   Bonus: Projects
    -   Assign a task to a project and filter tasks by project via a dropdown

## Tech Stack

-   PHP (Laravel)
-   MySQL
-   Optional Dev Environments:
    -   Docker
    -   Local PHP server

---

## Getting Started

You can run the app either with Docker (recommended) or on your local machine.

### Prerequisites

-   Git
-   Option A (Docker):
    -   Docker Desktop (or Docker Engine) and Docker Compose (docker compose)
-   Option B (Local):
    -   PHP >= 8.1 with required extensions
    -   Composer
    -   MySQL 8 (or compatible)

---

## Option A: Run with Docker

Docker provides an isolated environment for running this program.

Note:

-   Replace -ti taskmanager_app and <db_service> with the service/container names defined in your docker-compose.yml (you can find them there or via: docker compose ps).
-   The examples below assume the app is published on port 8040.

1. Clone the repository

-   git clone https://github.com/degod/task-manager-coalition.git
-   cd task-manager-coalition

2. Copy environment file

-   cp .env.example .env

3. Configure environment variables (adjust to match your docker-compose.yml)

-   DB_CONNECTION=mysql
-   DB_HOST=mysql
-   DB_PORT=3306
-   DB_DATABASE=laravel
-   DB_USERNAME=admin
-   DB_PASSWORD=admin
-   APP_URL=http://localhost:8040

4. Build and start containers

-   docker compose up -d --build

5. Install PHP dependencies (inside the PHP container)

-   docker compose exec -ti taskmanager_app composer install

6. Generate app key

-   docker compose exec -ti taskmanager_app php artisan key:generate

7. Run migrations (and seed if seeds exist)

-   docker compose exec -ti taskmanager_app php artisan migrate --seed

8. Access the app

-   http://localhost:8040

To stop:

-   docker compose down

### Shell access to run Artisan commands

Open an interactive bash shell in the PHP container:

-   docker compose exec -ti taskmanager_app bash

From inside the shell you can run:

-   php artisan migrate
-   php artisan tinker
-   php artisan optimize:clear
-   exit # leave the container

Run a single command without opening a shell:

-   docker compose exec -ti taskmanager_app php artisan migrate --seed

---

## Option B: Run with Local PHP Server

1. Clone the repository

-   git clone https://github.com/degod/task-manager-coalition.git
-   cd task-manager-coalition

2. Install dependencies

-   composer install

3. Copy environment file

-   cp .env.example .env

4. Create a database and configure .env (example)

-   DB_CONNECTION=mysql
-   DB_HOST=127.0.0.1
-   DB_PORT=3306
-   DB_DATABASE=laravel
-   DB_USERNAME=admin
-   DB_PASSWORD=admin
-   APP_URL=http://127.0.0.1:8000

5. Generate app key

-   php artisan key:generate

6. Run migrations (and seed if seeds exist)

-   php artisan migrate --seed

7. Start the local server

-   php artisan serve
-   The app will be available at http://127.0.0.1:8000

---

## Running Tests

-   Locally:

    -   php artisan test
    -   or: ./vendor/bin/phpunit

-   Inside Docker:
    -   docker compose exec -ti taskmanager_app php artisan test
    -   or: docker compose exec -ti taskmanager_app ./vendor/bin/phpunit

---

## Usage

-   Create, edit, and delete tasks via the UI.
-   Reorder tasks by dragging them; priorities update automatically based on their vertical order.
-   Use the Project dropdown to filter tasks by project (if projects are enabled in your build).

---

## Environment Variables

Common settings in .env:

-   APP_NAME="Task Manager"
-   APP_ENV=local
-   APP_DEBUG=true
-   APP_URL=http://localhost:8040 (Docker) or http://127.0.0.1:8000 (Local)
-   DB_CONNECTION=mysql
-   DB_HOST=<db_service> (Docker) or 127.0.0.1 (Local)
-   DB_PORT=3306
-   DB_DATABASE, DB_USERNAME, DB_PASSWORD set appropriately

---

## Troubleshooting

-   Port conflicts

    -   If MySQL is already running locally, stop it or change the port in docker-compose or in your local MySQL.

-   Permissions on Linux/macOS

    -   If you see permission issues with storage or bootstrap/cache:
        -   Locally: chmod -R ug+rwx storage bootstrap/cache
        -   Docker: docker compose exec -ti taskmanager_app chmod -R ug+rwx storage bootstrap/cache

-   Missing storage symlink for public files

    -   Locally: php artisan storage:link
    -   Docker: docker compose exec -ti taskmanager_app php artisan storage:link

-   Cache issues

    -   Locally: php artisan optimize:clear
    -   Docker: docker compose exec -ti taskmanager_app php artisan optimize:clear

-   Composer memory errors
    -   COMPOSER_MEMORY_LIMIT=-1 composer install

---

## Contributing

If you encounter bugs or wish to contribute, please follow these steps:

-   Fork the repository and clone it locally.
-   Create a new branch (git checkout -b feature/fix-issue).
-   Make your changes and commit them (git commit -am 'Fix issue').
-   Push to the branch (git push origin feature/fix-issue).
-   Create a new Pull Request against the main branch, tagging @degod.

---

## Contact

For inquiries or assistance, reach out to:

-   Email: degodtest@gmail.com
-   Phone: +2348024245093
