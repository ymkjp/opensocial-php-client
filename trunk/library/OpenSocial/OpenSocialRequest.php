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
  private $requestor;
  
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
   * For the two-legged OAuth case, some requests need a viewer context.  This
   * method assigns the appropriate VIEWER id to the xoauth_requestor_id
   * OAuth parameter.
   * @param string $id ID of the VIEWER.
   */
  public function setRequestor($id) {
    // TODO: Parse the user_id to see if it's an ID number or @me, etc
    $this->requestor = $id;
  }
  
  /**
   * Returns this request's requestor, if set.
   * @return string The value passed to setRequestor().
   */
  public function getRequestor() {
    return $this->requestor;
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
    
    if (isSet($this->requestor)) {
      $request->setRequestor($this->requestor);
    }
    
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
   * @param string $protocol Either "REST" or "RPC" depending on the protocol 
   *     used in the request.
   * @return mixed An initialized OpenSocial client library object.
   */
  abstract function processJsonResponse($response, $protocol);
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
  public function __construct($user_id, $group_id, $params=null, $id=null) {
    parent::__construct($id);
    
    if (!isSet($params)) {
      $params = array();
    }
    
    // Set up the REST request.
    $this->setRequestor($user_id);
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
  public function processJsonResponse($response, $protocol) {
    $start = $response["startIndex"];
    $total = $response["totalResults"];
    $data = ($protocol == "REST") ? $response["entry"] : $response["list"];
    return OpenSocialPerson::parseJsonCollection($start, $total, $data);
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
  public function processJsonResponse($response, $protocol) {
    $data = ($protocol == "REST") ? $response["entry"] : $response;
    return OpenSocialPerson::parseJson($data);
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
    
    // Set up the REST request.
    $this->setRequestor($user_id);
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
  public function processJsonResponse($response, $protocol) {
    $data = ($protocol == "REST") ? $response["entry"] : $response;
    return OpenSocialAppData::parseJson($data);
  }
}


/**
 * Represents a request to get app data.
 */
class UpdateAppDataRequest extends OpenSocialRequest {
  /**
   * Constructor.
   * @param string $user_id The ID of the user to update app data for.
   * @param array $data An associative array of key value pairs representing
   *     the App Data to set.
   * @param string $id A string identifier for this request.
   */
  public function __construct($user_id, $data, $id=null) {
    parent::__construct($id);
        
    // Map the data into app data
    $app_data = new OpenSocialAppData($data);
    $keys = array_keys($data);
        
    // Set up the REST request.
    $this->setRequestor($user_id);
    $params = array(
        "fields" => implode(",", $keys)
    );
    $url = sprintf("/appdata/@viewer/@self/@app", $user_id);
    $this->setRestParams("PUT", $url, $params, $app_data->toJsonObject());
    
    // Set up the RPC request.
    $rpc_params = array(
        "userId" => "@viewer",
        "groupId" => "@self",
        "appId" => "@app",
        "data" => $app_data->toJsonObject(),
        "fields" => $keys
    );
    $this->setRpcParams("appdata", "update", $rpc_params);
  }
  
  /**
   * Converts a valid JSON response to an OpenSocialAppData object.
   * @param mixed $response A JSON parsed response from the server.
   * @return OpenSocialAppData An object representing the returned app data.
   */
  public function processJsonResponse($response, $protocol) {
    OSLOG("UpdateAppDataRequest::processJsonResponse - response", $response);
    return True;
  }
}