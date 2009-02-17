#!/bin/bash
# Script to package up the library for distribution, all pretty like

rm -r dist
mkdir dist
svn export src dist/osapi
svn export examples dist/osapi/examples
for file in dist/osapi/examples/*.php
do
  echo Replacing include path on $file
  awk '{gsub(/..\/src/, ".."); print}' $file > $file.new
  rm $file
  mv $file.new $file
done

cp LICENSE README NOTICE dist/osapi
tar --directory dist -czf dist/osapi.tar.gz osapi


