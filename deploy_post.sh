#!/bin/sh
find /home/phpug/php.ug/src/public/ -maxdepth 1 -mindepth 1 -type d -exec cp -r {} /home/phpug/html/ \;
