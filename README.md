# Framework
The framework that powers [Mamook](https://github.com/Mamook/Mamook)

## Install

### Option #1: Composer Create Project
Make sure your project directory is empty and run `composer create-project mamook/framework .`

### Option #2: Composer Install (still needs work)

Make sure you're in your root project directory.

* `composer install`
* `cp -r vendor/mamook/bodega . && cp vendor/mamook/cache . && cp -r vendor/mamook/cron . && mkdir custom_sessions && cp -r vendor/mamook/data . && cp -r vendor/mamook/dev . && mkdir logs && cp -r vendor/mamook/tmp . && cp -r vendor/mamook/public . && cp vendor/mamook/settings.php .`
* `chmod 777 bodega/ bodega/audio/ bodega/cv/ bodega/premium/ bodega/videos/ cache/ custom_sessions/ data/formmail.ini logs/ public/images/ public/images/original/ public/media/audio/files/ public/media/videos/files/ tmp/`

## License

The Mamook framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
