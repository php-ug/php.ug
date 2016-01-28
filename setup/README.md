# Setup PHP.ug on your local machine

## Fork the project

* Fork the project on github into your own github-account
* On your machine clone the project into a local folder BASE_FOLDER

## Setup Database

* Create a new Mysql-Database named ```phpug```.
* Import the SQL-Dump ```phpug.sql.zip``` into that database

## Setup NGINX

* Create a new site based on ```php.ug.local.conf```. This requires a running nginx-webserver and a PHP-FPM installation.
* Replace the ```[/path/to]``` with the path to your BASE_PATH
* reload nginx

## Setup application

* copy ```src/config/application.config.php.dist``` to ```src/config/application.config.php```
* copy the files from ```setup/autoload/``` to ```src/config/autoload/``` and replace the appropriate tokens.

For a minimum working system you'll need to replace the database-information with the access-infos to your database.

If you need to be able to login you'll need to create a twitter-app at [apps.twitter.com](https://apps.twitter.com) and add the appropriate tokens to the configuration.
You will also want to replace ```[TWITTER_USERNAME]``` in ```setup/autoload/module.phpug.acl.local.php```.



