## Installation Process

To install, you'll want to clone or download this repo:

```
https://github.com/sudesh-agrawal/test-task-by-sudesh-agrawal.git
```


Next, we can install project with these **4 simple steps**:

### 1. Create a New Database

We'll need to utilize a MySQL database during the installation. For the following stage, you'll need to create a new database and preserve the credentials.

### 2. Copy the `.env.example` file

We need to specify our Environment variables for our application. You will see a file named `.env.example`, you will need to duplicate that file and rename it to `.env`.

Then, open up the `.env` file and update your *DB_DATABASE*, *DB_USERNAME*, and *DB_PASSWORD* in the appropriate fields.

```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wallet
DB_USERNAME=root
DB_PASSWORD=
```



### 3. Add Composer Dependencies

Following that, we'll need to install all composer dependencies through the following command:
```php
composer install
```

### 4. Run Migrations and Seeds

We must migrate our database schema into our database, which we can accomplish by running the following command:
```php
php artisan migrate
```

Finally, we will need to seed our database with the following command:

```php
php artisan db:seed
```

```php
php artisan key:generate
```

### 5. Start Server

We need to start server, if we are working on localhost:
```php
php artisan serve
```
