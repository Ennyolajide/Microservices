notifications-service

Introduction
This service handles notifications for our application.

Getting Started
1. cd into users-service directory
2. Install dependencies: `composer install`
3. Rename `example.env` to `.env` and configure your environment variables.
4. Generate application key for both services by running:
```bash
php artisan key:generate
```

Running Tests
To run tests for the notifications service, execute the following command:

```bash
php artisan test
