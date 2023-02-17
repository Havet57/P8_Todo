# P8_Todo

Improvement of an existing project ToDo & Co.

## Installation

1. Clone or download the GitHub repository :
    ```git clone https://github.com/Havet57/P8_Todo```
    
2. To configure your environment variables, including the database connection, create a local .env file at the root of the project by copying the .env file, and set the connection to the test database in the .env.test file.

3. Download and install project dependencies with Composer:
    ```composer install```

4. Create the database:
   ```php bin/console doctrine:database:create```
   
5. Create the different tables in the database with a migration:
```php bin/console doctrine:migrations:migrate```

6. Install the fixtures to have a minimal demo data in development:
```php app/console doctrine:fixtures:load```
