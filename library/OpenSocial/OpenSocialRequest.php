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

/**
 * OpenSocial Client Library for PHP
 * 
 * @package OpenSocial
 */
 
require_once("Zend/Json.php");
require_once("OpenSocial/OpenSocialPerson.php");

/**
 * Represents an atomic OpenSocial operation that can be converted to either
 * a REST or RPC request.
 */
abstract class OpenSocialRequest {
  private $rest_url;
  private $rest_data;
  private $rest_params;
  private $rest_method;
  private $rpc_entity;
  private $rpc_operation;
  private $rpc_params;
  
  /**
   * Constructor
   */
  public function __construct($id=null) {
    if (isSet($id)) {
      $this->id = $id; 
    } else {
      // uniqid() only works in PHP5.
      $this->id = md5(uniqid());
    }
  } 
  
  protected function setRestParams($method, $url, $params=null, $data=null) {
    $this->rest_url = $url;
    $this->rest_data = $data;
    $this->rest_params = $params;
    $this->rest_method = $method;
  }
  
  protected function setRpcParams($entity, $operation, $params=null) {
    $this->rpc_entity = $entity;
    $this->rpc_operation = $operation;
    $this->rpc_params = $params;
  }
  
  public function getId() {
    return $this->id;
  }
  
  public function getRestRequest($base_url="") {
    $url = $base_url . $this->rest_url;
    $request = new OpenSocialHttpRequest(
        $this->rest_method, 
        $url, 
        $this->rest_params, 
        $this->rest_data
    );
    return $request;
  }
  
  public function getRpcBody() {
    $body = array(
        "method" => sprintf("%s.%s", $this->rpc_entity, $this->rpc_operation),
        "params" => $this->rpc_params,
        "id" => $this->id
    );
    
    return $body;
  }
  
  abstract function processJsonResponse($response);
  
}


/**
 * Represents a request for multiple people.
 */
class FetchPeopleRequest extends OpenSocialRequest {
  /**
   * Constructor.
   * @param string @operation One of "get", "create", "update", or "delete".
   * @param string @user_id The user id to fetch for this request.
   */
  public function __construct($user_id, $group_id, $params=array(), $id=null) {
    parent::__construct($id);
    
    // TODO: There's probably a better place to inject xoauth_requestor_id.
    // TODO: Parse the user_id to see if it's an ID number or @me, etc
    $params["xoauth_requestor_id"] = $user_id;
    
    // Set up the REST request.
    $url = sprintf("/people/%s/%s", $user_id, $group_id);
    $this->setRestParams("GET", $url, $params);
    
    // Set up the RPC request.
    $rpc_params = array_merge($params, array(
        "userId" => $user_id,
        "groupId" => $group_id
    ));
    $this->setRpcParams("people", "get", $rpc_params);
  }
  
  /**
   * Converts a json response to a collection of people.
   * @param string $response Text of a successful response from the server.
   * @return OpenSocialCollection A Collection of Person instances.
   */
  public function processJsonResponse($response) {
    return OpenSocialPerson::parseJsonCollection($response);
  }
}


/**
 * Represents a request working with a single person.
 */
class FetchPersonRequest extends FetchPeopleRequest {
  public function __construct($user_id, $params=array(), $id=null) {
    parent::__construct($user_id, "@self", $params, $id);
  }
  
  /**
   * Converts a json response to a single person object.
   * @param string $response Text of a successful response from the server.
   * @return OpenSocialPerson A Person instance.
   */
  public function processJsonResponse($response) {
    return OpenSocialPerson::parseJson($response);
  }
}