## How to install
```
$ cd [YOUR PROJECT FOLDER]
$ git clone https://github.com/kondows95/laravel-spa-api-startup-kit.git laravel
$ cd laravel

$ cp .env.example .env
$ vi .env
DB_HOST=127.0.0.1
DB_DATABASE=sample_db
DB_USERNAME=root
DB_PASSWORD=

$ vi .env.testing
DB_HOST=127.0.0.1
DB_DATABASE=sample_db_testing
DB_USERNAME=root
DB_PASSWORD=

(MySQL5.7 needed)
$ mysql -u root
mysql> CREATE SCHEMA `sample_db` DEFAULT CHARACTER SET utf8;
mysql> CREATE SCHEMA `sample_db_testing` DEFAULT CHARACTER SET utf8;
mysql> quit;

$ composer install
$ php artisan key:generate

$ php artisan migrate --seed
$ php artisan migrate --seed --env=testing

$ ./vendor/bin/phpunit
```
