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
  /**
   * Initializes the test class
   */
  public function setUp() { }
  
  /**
   * Cleans up after each test.
   */
  public function tearDown() { }
  
  /**
   * Test whether passing strings with an ending / or not break the library.
   */
  public function testUrlConfigStrings() {
    $httplib = new MockHttpLib();
    $req = new FetchPersonRequest("12345");
    
    $client_a = new OpenSocial(array(
      "server_rest_base" => "http://example.com/social/rest/"
    ), $httplib);
    
    $client_b = new OpenSocial(array(
      "server_rest_base" => "http://example.com/social/rest"
    ), $httplib);
      

    $client_a->request($req);
    $http_req_a = $httplib->getRequest();
    
    $client_b->request($req);
    $http_req_b = $httplib->getRequest();
    
    $this->assertEquals(
        $http_req_a->getNormalizedUrl(),
        $http_req_b->getNormalizedUrl()
    );
  }
}
  