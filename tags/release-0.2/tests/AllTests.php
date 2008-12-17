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
require_once("OnlineTests.php");
require_once("OfflineTests.php");

/**
 * Aggregates all of the test classes so that they may be run at once.
 */
class AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite("PHPUnit");
        $suite->addTest(OnlineTests::suite());
        $suite->addTest(OfflineTests::suite());
        return $suite;
    }
}
