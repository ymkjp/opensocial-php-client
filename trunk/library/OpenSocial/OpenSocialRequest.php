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
require_once("OpenSocial/OpenSocialAppData.php");

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
  
  /**
   * Set parameters for this object to be converted to a REST request.
   * @param string $method The HTTP method to use.
   * @param string $url The url to fetch.  This may be a relative URL from the
   *     REST endpoint.
   * @param array $params Additional querystring parameters to use in the 
   *     request.
   * @param string $data POST body data to be sent in the request.
   */
  protected function setRestParams($method, $url, $params=null, $data=null) {
    $this->rest_url = $url;
    $this->rest_data = $data;
    $this->rest_params = $params;
    $this->rest_method = $method;
  }
  
  /**
   * Set parameters for this object to be converted to a RPC request body.
   * @param string $entity The RPC entity that this request operates on.
   * @param string $operation The operation to perform.
   * @param array $params Additional querystring parameters to use in the 
   *     request.
   */
  protected function setRpcParams($entity, $operation, $params=null) {
    $this->rpc_entity = $entity;
    $this->rpc_operation = $operation;
    $this->rpc_params = $params;
  }
  
  /**
   * Returns the string ID of this request.
   * @return string An ID tagging this request.
   */
  public function getId() {
    return $this->id;
  }
  
  /**
   * Returns an OpenSocialHttpRequest object that may be sent to an
   * OpenSocialHttpLib object.
   * @param string $base_url The REST base URL for a server.  Used to resolve
   *     relative paths for this request.
   * @return OpenSocialHttpRequest A request object that is not signed.
   */
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
  
  /**
   * Returns an object that is suitable for building an RPC request.
   * @return array An array with parameters corresponding with those needed
   *     to perform this request as an RPC operation.
   */
  public function getRpcBody() {
    $body = array(
        "method" => sprintf("%s.%s", $this->rpc_entity, $this->rpc_operation),
        "params" => $this->rpc_params,
        "id" => $this->id
    );
    return $body;
  }
  
  /**
   * Abstract function that should parse a JSON object and return a native
   * library object.
   * @param mixed $response A response that has been JSON decoded.
   * @return mixed An initialized OpenSocial client library object.
   */
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
  /**
   * Constructor.
   * @param string $user_id Id of the user to fetch.
   * @param array $params Additional request parameters.
   * @param string $id String identifier for this request.
   */
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


/**
 * Represents a request to get app data.
 */
class FetchAppDataRequest extends OpenSocialRequest {
  /**
   * Constructor.
   * @param string user_id ID of the user for who this request is targeted.
   * @param string group_id  The group of users from which to fetch app data.
   * @param array keys An array of strings representing app data keys to fetch.
   * @param array params Additional parameters for the request.
   * @param string id String identifier for this request.
   */
  public function __construct($user_id, $group_id, $app_id=null, $keys=null,
      $params=array(), $id=null) {
    parent::__construct($id);
    
    if (!isSet($app_id)) {
      $app_id = "@app";
    }
    
    // TODO: There's probably a better place to inject xoauth_requestor_id.
    // TODO: Parse the user_id to see if it's an ID number or @me, etc
    $params["xoauth_requestor_id"] = $user_id;
    
    // Set up the REST request.
    $rest_params = $params;    // PHP should value copy this array.
    $url = sprintf("/appdata/%s/%s/%s", $user_id, $group_id, $app_id);
    if (isSet($keys)) {
      $rest_params["fields"] = join(",", $keys);
    }
    $this->setRestParams("GET", $url, $rest_params);
    
    // Set up the RPC request.
    $rpc_params = array_merge($params, array(
        "userId" => $user_id,
        "groupId" => $group_id,
        "appId" => $app_id,
        "keys" => $keys
    ));
    $this->setRpcParams("appdata", "get", $rpc_params);
  }
  
  /**
   * Converts a valid JSON response to an OpenSocialAppData object.
   * @param mixed $response A JSON parsed response from the server.
   * @return OpenSocialAppData An object representing the returned app data.
   */
  public function processJsonResponse($response) {
    return OpenSocialAppData::parseJson($response);
  }
}