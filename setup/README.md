# Setup PHP.ug on your local machine

## Fork the project

* Fork the project on github into your own github-account
* On your machine clone the project into a local folder BASE_FOLDER

## Setup Database

* Create a new Mysql-Database named ```phpug```
* Import the SQL-Dump ```phpug.sql.zip``` into that database

## Setup NGINX

* Create a new site based on ```php.ug.local.conf```. This requires a running nginx-webserver and a PHP-FPM installation.
* Replace the ```[/path/to]``` with the path to your BASE_PATH
* Reload nginx

## Install Dependencies

* Run ```composer install```
* Run ```bower install```

## Setup application

* Copy ```src/config/application.config.php.dist``` to ```src/config/application.config.php```
* Copy the files from ```setup/autoload/``` to ```src/config/autoload/``` and replace the appropriate tokens
* Provide your database access information in ```src/config/autoload/module.doctrine_orm.local.php```

If you need to be able to login you'll need to create a twitter-app at [apps.twitter.com](https://apps.twitter.com) and add the appropriate tokens to the configuration.
You will also want to replace ```[TWITTER_USERNAME]``` in ```setup/autoload/module.phpug.acl.local.php```.



