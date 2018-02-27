# Framework
The framework that powers [Mamook](https://github.com/Mamook/Mamook)

## Install

### Option #1: Composer Create Project
Make sure your project directory is empty and run `composer create-project mamook/framework .`

### Option #2: Composer Install (still needs work)

Make sure you're in your root project directory.

* `composer require mamook/framework`
* `composer run-script post-install-cmd -d ./vendor/mamook/framework/`
* `cp -r vendor/mamook/framework/bodega . && mkdir -p cache/screenshots && cp -r vendor/mamook/framework/cron . && mkdir custom_sessions && cp -r vendor/mamook/framework/data . && cp -r vendor/mamook/framework/dev . && mkdir logs && mkdir -p tmp/fm && cp -r vendor/mamook/framework/public . && cp vendor/mamook/framework/.env . && cp vendor/mamook/framework/settings.php .`
* `chmod 777 bodega/ bodega/audio/ bodega/cv/ bodega/premium/ bodega/videos/ cache/ custom_sessions/ data/formmail.ini logs/ public/images/ public/images/original/ public/media/audio/files/ public/media/videos/files/ tmp/ tmp/fm`

## License

The Mamook framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
