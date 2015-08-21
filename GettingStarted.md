This client library helps you work with social network data from your web server.  This document will help explain where you can obtain the files for the client library, and how to use them in your own projects.



# Obtaining the client library #

There are two options for obtaining the files for the client library.

## Obtaining the pre-packaged release ##
Most developers will want to use the pre-packaged release, which is released according to the [project roadmap](ProjectRoadmap.md) and will be the most stable version of the library.

To download the library, go to the [Downloads tab](http://code.google.com/p/opensocial-php-client/downloads/list).  The most recent release of the library will be listed, typically with a few different archive types (.zip, .tar.gz, .tar.bz).  There is no difference between the different archive formats - please select one supported by your operating system.

After extracting the library from the archive, you will have a new `opensocial-php-client` directory.  The library files themselves are in the `opensocial-php-client/osapi` directory.

## Obtaining the most up-to-date version from SVN ##
The most up-to-date version of the code is available from this project's SVN repository.  Obtaining the code this way is intended for developers looking for fixes or features that have not been released in the pre-packaged version, or for developers who want to contribute patches back to the project.

Obtain the code by using the following SVN checkout command (you will need an SVN client installed on your computer):
```
  svn checkout http://opensocial-php-client.googlecode.com/svn/trunk/ opensocial-php-client
```

After checking out the code, you will have a new `opensocial-php-client` directory.  The library files themselves are in the `opensocial-php-client/src/osapi` directory.


# What to do with the files #

After obtaining the library in either of the ways described above, you will have a directory named `osapi` somewhere on your filesystem, containing the library files.  Specifically, you will need to include the file `osapi/osapi.php` inside of your scripts.

To be able to include `osapi.php` inside of your PHP scripts, you will need to tell PHP where it can find the client library.  Listed below are some suggested ways of doing this.

## Copy the `osapi` directory into your project ##

You can make a copy of the `osapi` directory for each PHP project you write.  For example, if your project structure looks like this:
```
  myproject/
    |- myproject.php
```

Copy the `osapi` directory into the `myproject` directory.  You can add the following code to `myproject.php` to include the client library:
```
  require_once "osapi/osapi.php";
```

## Setting `include_path` by editing `php.ini` ##

If you don't want to make a copy of the library directory for each of your projects, you can edit the `php.ini` PHP configuration file on your server and tell PHP where to find the client library.

The line that configures `include_path` will contain some existing paths on your system:
```
  include_path=".:/usr/local/share/pear:/usr/local/PEAR"
```

Add a colon, followed by the path to your `osapi` directory:
```
  include_path=".:/usr/local/share/pear:/usr/local/PEAR:/path/to/osapi"
```

Once you restart your web server, you can include the library by using the following line in your PHP scripts:
```
  require_once "osapi.php";
```

## Setting `include_path` dynamically inside of your code ##

If you are unable to edit your `php.ini` file, you can still adjust the value of `include_path` inside of your PHP scripts.  Just include the following code:
```
  set_include_path(get_include_path() . PATH_SEPARATOR . '/path/to/osapi');
  require_once "osapi.php";
```