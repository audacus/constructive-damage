# constructive-damage
A 100% modular browser game.

## Requirements
* Allow .htaccess override
* Makes use of the [php-autoloader](https://github.com/audacus/php-autoloader) for autoloading the classes.
* Makes use of the [php-error-handler](https://github.com/audacus/php-error-handler) for handling errors and exceptions.
* Makes use of the [php-rest-api](https://github.com/audacus/php-rest-api) for handling rest requests.
* The app/config/application.json should be added to overwrite default config.
* Adjust the NotORM requirement call in app/index.php if necessary (default: 'notorm/NotORM.php').
