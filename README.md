# Microservices Project

This project contains multiple microservices for handling different functionalities.

## Services

- [notifications-service](./notifications-service/README.md): Microservice responsible for handling notifications.
- [users-service](./users-service/README.md): Microservice responsible for managing users.

For more detailed information about each service, please refer to their respective README files.

## Getting Started
1. Clone this repository.
2. Run `docker-compose up -d` to start the services.
3. Install dependencies for each service:
   - notifications-service: Navigate to the `notifications-service` directory and run `composer install`.
   - users-service: Navigate to the `users-service` directory and run `composer install`.
4. Rename `example.env` to `.env` for each service and configure your environment variables.
5. Generate application key for both services by running:
```bash
php artisan key:generate
```
6. For users-service only create db file and run migration:
```bash
touch database/database.sqlite && php artisan migrate
```


## Running Tests
To run tests for each service, navigate to the respective directory and execute the following command:

```bash
php artisan test
