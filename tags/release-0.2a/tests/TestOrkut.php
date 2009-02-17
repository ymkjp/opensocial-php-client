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

class TestOrkut extends AbstractTestContainer {
  protected function getRestConfig() {
    return array(
      "oauth_consumer_key" => "orkut.com:623061448914",
      "oauth_consumer_secret" => "uynAeXiWTisflWX99KU1D2q5",
      "server_rest_base" => "http://sandbox.orkut.com/social/rest/"
    );
  }
  protected function getRpcConfig() {
    return array(
      "oauth_consumer_key" => "orkut.com:623061448914",
      "oauth_consumer_secret" => "uynAeXiWTisflWX99KU1D2q5",
      "server_rpc_base" => "http://sandbox.orkut.com/social/rpc/"
    );
  }  
  

  /**
   * Does a live fetch person test against orkut using sockets.
   */
  public function testOrkutSocketFetchPerson() {
    $httplib = new SocketHttpLib();
    $orkut_client = new OpenSocial($this->getRestConfig(), $httplib);
    $this->validateFetchPerson($orkut_client);
  }
  
  /**
   * Does a live fetch person test against orkut using curl.
   */
  public function testOrkutCurlFetchPerson() {
    $httplib = new CurlHttpLib();
    $orkut_client = new OpenSocial($this->getRestConfig(), $httplib);
    $this->validateFetchPerson($orkut_client);
  }
  
  /**
   * Validates the fetch AppData tests.
   */ 
  protected function validateFetchAppData($client) {
    $req = new FetchAppDataRequest("03067092798963641994", "@self");
    $app_data = $client->request($req);
    $this->assertTrue(is_array($app_data["03067092798963641994"]));
    $this->assertEquals(1, count($app_data));
  }
  
  /**
   * Validates the update AppData tests.
   */ 
  protected function validateUpdateAppData($client) {
    $timestamp = time();
    $date = date('l jS \of F Y h:i:s A');
    $app_data = array(
        "timestamp" => $timestamp,
        "date" => $date          
    );
    $req = new UpdateAppDataRequest("03067092798963641994", $app_data);
    $response = $client->request($req);
    
    $req_2 = new FetchAppDataRequest("03067092798963641994", "@self");
    $response_2 = $client->request($req_2);
    $ret_data = $response_2["03067092798963641994"];
    $this->assertEquals($date, $ret_data["date"]); 
    $this->assertEquals($timestamp, $ret_data["timestamp"]);   
  }
  
  /**
   * Tests an instance of a client against expected live orkut data.
   */
  protected function validateFetchPerson($client) {
    $person = $client->fetchPerson("03067092798963641994");
    $this->assertEquals("03067092798963641994", $person->getId());
  }
  
  /**
   * Tests an instance of a client against expected live orkut data.
   */
  protected function validateFetchPeople($client) {
    $req = new FetchPeopleRequest("03067092798963641994", "@friends");
    $result = $client->request($req);

    // TODO: These are pretty brittle tests - if the test orkut account adds
    // a friend, these tests break :(
    $this->assertEquals(count($result), 6);
    $this->assertEquals($result->startIndex, 0);
    $this->assertEquals($result->totalResults, 6);
    $this->assertEquals("13314698784882897227", $result[0]->getId());
    $this->assertEquals("04285289033838943214", $result[1]->getId());
  }
  
  /**
   * Tests creating an activity on orkut.
   */
  protected function validateCreateActivity($client) {
    $date = date('l jS \of F Y h:i:s A');
    $req = new CreateActivityRequest(
        "03067092798963641994", "Test activity", $date);
    $result = $client->request($req);
    
    // TODO: assertions here (orkut actually fails this test right now)
  }
}
