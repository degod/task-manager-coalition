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
    -   Docker (Laravel Sail)
    -   Local PHP server

---

## Getting Started

You can run the app either with Docker (recommended) or on your local machine.

### Prerequisites

-   Git
-   Option A (Docker):
    -   Docker Desktop (or Docker Engine) and Docker Compose
-   Option B (Local):
    -   PHP >= 8.1 with required extensions (bcmath, ctype, fileinfo, json, mbstring, openssl, PDO, tokenizer, xml)
    -   Composer
    -   MySQL 8 (or compatible)
    -   Node.js + npm/yarn (only if you need to build assets)

---

## Option A: Run with Docker (Laravel Sail)

Laravel Sail provides a lightweight Docker environment for Laravel.

1. Clone the repository

-   git clone https://github.com/your-org/your-repo.git
-   cd your-repo

2. Copy environment file

-   cp .env.example .env

3. Set environment variables for Sail
   In .env ensure the following (adjust if needed):

-   DB_CONNECTION=mysql
-   DB_HOST=mysql
-   DB_PORT=3306
-   DB_DATABASE=laravel
-   DB_USERNAME=sail
-   DB_PASSWORD=password
-   APP_URL=http://localhost

4. Install PHP dependencies

-   If you have Composer installed locally:
    -   composer install
-   If you don’t have Composer locally, use the Composer Docker image:
    -   docker run --rm -u "$(id -u):$(id -g)" -v "$PWD":/app -w /app composer install --no-interaction --prefer-dist

5. Install Sail (if not already present in the project)

-   composer require laravel/sail --dev
-   php artisan sail:install
    Choose MySQL when prompted. If the project already has Sail set up, you can skip this step.

6. Start the containers

-   ./vendor/bin/sail up -d

7. Generate app key

-   ./vendor/bin/sail artisan key:generate

8. Run migrations (and seed if seeds exist)

-   ./vendor/bin/sail artisan migrate --seed

9. (Optional) Build frontend assets

-   ./vendor/bin/sail npm install
-   ./vendor/bin/sail npm run dev

10. Access the app

-   http://localhost

To stop:

-   ./vendor/bin/sail down

---

## Option B: Run with Local PHP Server

1. Clone the repository

-   git clone https://github.com/your-org/your-repo.git
-   cd your-repo

2. Install dependencies

-   composer install

3. Copy environment file

-   cp .env.example .env

4. Create a database and configure .env
   Ensure your .env has valid DB settings, for example:

-   DB_CONNECTION=mysql
-   DB_HOST=127.0.0.1
-   DB_PORT=3306
-   DB_DATABASE=task_manager
-   DB_USERNAME=your_mysql_user
-   DB_PASSWORD=your_mysql_password
-   APP_URL=http://127.0.0.1:8000

5. Generate app key

-   php artisan key:generate

6. Run migrations (and seed if seeds exist)

-   php artisan migrate --seed

7. (Optional) Build frontend assets

-   npm install
-   npm run dev

8. Start the local server

-   php artisan serve
    The app will be available at http://127.0.0.1:8000

---

## Usage

-   Create, edit, and delete tasks via the UI.
-   Reorder tasks by dragging them; priorities update automatically based on their vertical order.
-   Use the Project dropdown to filter tasks by project (if projects are enabled in your build).

---

## Environment Variables

Common settings in .env:

-   APP_NAME=Task Manager
-   APP_ENV=local
-   APP_DEBUG=true
-   APP_URL=http://localhost (Docker) or http://127.0.0.1:8000 (Local)
-   DB_CONNECTION=mysql
-   DB_HOST=mysql (Docker/Sail) or 127.0.0.1 (Local)
-   DB_PORT=3306
-   DB_DATABASE, DB_USERNAME, DB_PASSWORD set appropriately

If using Sail defaults:

-   DB_USERNAME=sail
-   DB_PASSWORD=password

---

## Troubleshooting

-   Port conflicts
    -   If MySQL is already running locally, stop it or change the port in docker-compose/Sail or in your local MySQL.
-   Permissions on Linux/macOS
    -   If you see permission issues with storage or bootstrap/cache:
        -   For Sail: ./vendor/bin/sail artisan storage:link
        -   chmod -R ug+rwx storage bootstrap/cache
-   Cache issues
    -   php artisan optimize:clear
    -   For Sail: ./vendor/bin/sail artisan optimize:clear
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
