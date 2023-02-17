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
   
5. And the database test : 
   ```php bin/console doctrine:database:create --env=test```

   
6. Create the different tables in the database :
```php bin/console doctrine:schema:update --force ```

7. And create the different tables in the database test :
```php bin/console doctrine:schema:update --force --env=test ```

8. Install the fixtures to have a minimal demo data in development:
```php bin/console doctrine:fixtures:load --group=dev -n```

9. Install the fixtures to have a minimal demo data in development:
```php bin/console doctrine:fixtures:load --group=test --env=test -n```
