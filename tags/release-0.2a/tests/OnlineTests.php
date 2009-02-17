<?php
/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once("PHPUnit/Framework.php");
require_once("TestOpenSocialHttpLib.php");
require_once("TestOrkut.php");
require_once("TestMySpace.php");

/**
 * Aggregates all of the online test classes. (Needs internet connectivity)
 */
class OnlineTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite("PHPUnit Online");
        $suite->addTestSuite("TestSocketHttpLib");
        $suite->addTestSuite("TestCurlHttpLib");
        $suite->addTestSuite("TestOrkut");
        $suite->addTestSuite("TestMySpace");
        return $suite;
    }
}
