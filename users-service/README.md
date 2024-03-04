users-service

Introduction
The users service manages user-related operations for our application.

Getting Started
1. cd into users-service directory
2. Install dependencies: `composer install`
3. Rename `example.env` to `.env` and configure your environment variables.
4. Generate application key for both services by running:
```bash
php artisan key:generate
```
5. Create db file and run migration:
```bash
touch database/database.sqlite && php artisan migrate
```

Running Tests
To run tests for the users service, execute the following command:

```bash
php artisan test
