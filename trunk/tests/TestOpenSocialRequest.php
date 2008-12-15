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
require_once('MockHttpLib.php');

/**
 *
 */
class TestOpenSocialRequest extends PHPUnit_Framework_TestCase {
  protected $httplib;
  protected $client;
  protected $config = array(
    "oauth_consumer_key" => "test_consumer_key",
    "oauth_consumer_secret" => "test_consumer_secret",
    "server_rest_base" => "http://example.com/rest/"
  );
  
  /**
   * Initializes the test class.
   */
  public function setUp() {
    $this->httplib = new MockHttpLib();
    $this->client = new OpenSocial($this->config, $this->httplib);
  }
  
  /**
   * Cleans up after each test.
   */
  public function tearDown() {
    unset($this->httplib);
    unset($this->client);
  }
  
  /**
   * General FetchPersonRequest test.
   */
  public function testFetchPersonRequest() {
    $req = new FetchPersonRequest("12345");
    $text_response = <<<EOM
{  
  "entry": { 
    "id":"12345",
    "isViewer":true,
    "isOwner":false,
    "name":{ "familyName":"Testington", "givenName":"Sample" }
  }
}
EOM;
    $this->httplib->setResponse($text_response);
    $result = $this->client->request($req);
    $http_req = $this->httplib->getRequest();
    
    $this->assertEquals("GET", 
         $http_req->getMethod());
    $this->assertEquals("http://example.com/rest/people/12345/@self",
        $http_req->getNormalizedUrl());
    $this->assertEquals("12345", 
        $http_req->getParameter("xoauth_requestor_id"));
    $this->assertEquals("test_consumer_key",
        $http_req->getParameter("oauth_consumer_key"));
        
    $this->assertEquals("12345",
        $result->getId());
    $this->assertEquals("Sample Testington", 
        $result->getDisplayName());
  }
}
