# Export config vars to Heroku
A Laravel artisan command to allow a quick export of config vars to Heroku with one command. Eliminates all that time spent manually inputting config vars either through the CLI or through the web app.

### Requirements
* Laravel 5
* PHP 7
* Heroku CLI
* Project to have a Heroku remote connected

### Installation
```bash
composer require jonathanport/artisan-export-config-vars-to-heroku
```

### Usage
Simply run:
```bash
php artisan export-config-vars-to-heroku
```

The command line will ask you to input a file name. This could be your default `.env` file but I personally like to make a separate `.env.heroku` file and use that to input all my config vars so I don't mess up my local file. Inside of the command line, type in `.your-env` and it will continue to import your config vars.

By default, the script will not export empty values e.g. `APP_NAME=`. To allow the export of empty values, you can add the `--include-empty-values` flag and the script will include empty values. The script will include `null` values regardless.


### Feedback
Feedback is welcome. Enjoy!