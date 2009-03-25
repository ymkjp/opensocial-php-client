#!/bin/bash
# Script to package up the library for distribution, all pretty like

svn export src opensocial-php-client
cp LICENSE README NOTICE opensocial-php-client

zip -r osapi-`date "+%Y%m%d"`.zip opensocial-php-client
tar -cjvf osapi-`date "+%Y%m%d"`.tar.bz opensocial-php-client
tar  -czf osapi-`date "+%Y%m%d"`.tar.gz opensocial-php-client

rm -r opensocial-php-client
