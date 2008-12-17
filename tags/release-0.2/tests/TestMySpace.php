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
require_once('AbstractTestContainer.php');

class TestMySpace extends AbstractTestContainer {
  protected function getRestConfig() {
    return array(
      "oauth_consumer_key" => "http://opensocial-resources.googlecode.com/svn/samples/rest_rpc/sample.xml",
      "oauth_consumer_secret" => "6a838d107daf4d09b7d446422f5e7a81",
      "server_rest_base" => "http://api.myspace.com/v2"
    );
  }
  protected function getRpcConfig() {
    return null;
  }  
  
  /**
   * Validates the fetch AppData tests.
   */ 
  protected function validateFetchAppData($client) {
    // MySpace currently doesn't support fetching App Data.
  }
  
  /**
   * Validates the update AppData tests.
   */ 
  protected function validateUpdateAppData($client) {
    // MySpace currently doesn't support updating App Data.
  }

  /**
   * Tests an instance of a client against expected live orkut data.
   */
  protected function validateFetchPerson($client) {
    $person = $client->fetchPerson("425505213");
    $this->assertEquals("myspace.com:425505213", $person->getId());
  }
  
  /**
   * Tests an instance of a client against expected live orkut data.
   */
  protected function validateFetchPeople($client) {
    $req = new FetchPeopleRequest("425505213", "@friends");
    $result = $client->request($req);

    $this->assertEquals(count($result), 4);
    $this->assertEquals($result->startIndex, 1);
    $this->assertEquals($result->totalResults, 4);
    $this->assertEquals($result[0]->getId(), "myspace.com:6221");
    $this->assertEquals($result[1]->getId(), "myspace.com:431404430");
  }
}
