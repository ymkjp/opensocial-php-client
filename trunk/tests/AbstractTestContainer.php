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

abstract class AbstractTestContainer extends PHPUnit_Framework_TestCase {
  private $rest_client;  
  private $rpc_client;
  
  /**
   * Initializes the test class with an OpenSocial object attached to a mock
   * server.
   */
  public function setUp() {
    $rest_config = $this->getRestConfig();
    $rpc_config = $this->getRpcConfig();
    if (isSet($rest_config)) {
      $this->rest_client = new OpenSocial($this->getRestConfig()); 
    }
    if (isSet($rpc_config)) {
      $this->rpc_client = new OpenSocial($this->getRpcConfig()); 
    }
  }
  
  /**
   * Removes the reference to the OpenSocial class instance.
   */
  public function tearDown() {
    unset($this->rest_client);
    unset($this->rpc_client);
  }
  
  public function testRestFetchAppData() {
    if (isSet($this->rest_client)) {
      $this->validateFetchAppData($this->rest_client);  
    }
  }
  
  public function testRpcFetchAppData() {
    if (isSet($this->rpc_client)) {
      $this->validateFetchAppData($this->rpc_client);  
    }
  }
  
  public function testRestUpdateAppData() {
    if (isSet($this->rest_client)) {
      $this->validateUpdateAppData($this->rest_client);  
    }
  }
  
  public function testRpcUpdateAppData() {
    if (isSet($this->rpc_client)) {
      $this->validateUpdateAppData($this->rpc_client);  
    } 
  }
  
  public function testRestFetchPerson() {
    if (isSet($this->rest_client)) {
      $this->validateFetchPerson($this->rest_client);  
    }
  }
  
  public function testRpcFetchPerson() {
    if (isSet($this->rpc_client)) {
      $this->validateFetchPerson($this->rpc_client);  
    }
  }
  
  public function testRestFetchPeople() {
    if (isSet($this->rest_client)) {
      $this->validateFetchPeople($this->rest_client);  
    }
  }
  
  public function testRpcFetchPeople() {
    if (isSet($this->rpc_client)) {
      $this->validateFetchPeople($this->rpc_client);  
    }
  }
  
  public function testRestCreateActivity() {
    if (isSet($this->rest_client)) {
      $this->validateCreateActivity($this->rest_client);  
    } 
  }
  
  public function testRpcCreateActivity() {
    if (isSet($this->rpc_client)) {
      $this->validateCreateActivity($this->rpc_client);  
    }
  }
  
  protected abstract function getRestConfig();
  protected abstract function getRpcConfig();
  protected function validateFetchAppData($client) {}
  protected function validateUpdateAppData($client) {}
  protected function validateFetchPerson($client) {}
  protected function validateFetchPeople($client) {}
  protected function validateCreateActivity($client) {}
}
