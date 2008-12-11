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
 
// Add the library directory to the include path
set_include_path(get_include_path() . PATH_SEPARATOR . 
    '..' . DIRECTORY_SEPARATOR . 'library');

require_once('PHPUnit/Framework.php');
require_once('OpenSocial/OpenSocial.php');

class TestOpenSocial extends PHPUnit_Framework_TestCase {
  protected $opensocial;
  
  /**
   * Initializes the test class with an OpenSocial object attached to a mock
   * server.
   */
  public function setUp() {
    $config = array(
      "oauth_consumer_key" => "orkut.com:623061448914",
      "oauth_consumer_secret" => "uynAeXiWTisflWX99KU1D2q5",
      "server_rest_base" => "http://sandbox.orkut.com/social/rest/"
    );
    $this->opensocial = new OpenSocial($config);
  }
  
  /**
   * Removes the reference to the OpenSocial class instance.
   */
  public function tearDown() {
    unset($this->opensocial);
  }
  
  /**
   * Placeholder test
   */
  public function testNothing() {
    $this->assertTrue(True);
  }
}
  