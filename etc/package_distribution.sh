#!/bin/bash
# Script to package up the library for distribution, all pretty like

svn export src opensocial-php-client
cp LICENSE README NOTICE VERSION opensocial-php-client

version=`cat VERSION`
zip -r opensocial-php-client-$version.zip opensocial-php-client
tar -cjvf opensocial-php-client-$version.tar.bz opensocial-php-client
tar  -czf opensocial-php-client-$version.tar.gz opensocial-php-client

rm -r opensocial-php-client
