<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

/*
 * This file is meant to be run through a php command line, and not called directly
 * through the web browser. To run these tests from the command line:
 * # cd /path/to/client
 * # phpunit AllTests test/AllTests.php   
 */
 
// Report everything
ini_set('error_reporting', E_ALL | E_STRICT);
 
 // Include the base library
set_include_path(get_include_path() . PATH_SEPARATOR . "src");
require_once "osapi/osapi.php";

// Use a default timezone or else strtotime will raise errors
date_default_timezone_set('America/Los_Angeles');

if (defined('PHPUnit_MAIN_METHOD') === false) {
  define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

class AllTests {

  public static function main() {
    PHPUnit_TextUI_TestRunner::run(self::suite(), array());
  }

  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite();
    $suite->setName('AllTests');
    $path = realpath('./test/');
    $testTypes = array('common', 'auth', 'io', 'model', 'providers', 'service', 'storage');
    foreach ($testTypes as $type) {
      foreach (glob("$path/{$type}/*Test.php") as $file) {
        if (is_readable($file)) {
          require_once $file;
          $className = str_replace('.php', '', basename($file));
          $suite->addTestSuite($className);
        }
      }
    }
    return $suite;
  }
}

if (PHPUnit_MAIN_METHOD === 'AllTests::main') {
  AllTests::main();
}