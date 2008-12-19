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

/**
 * Set this to True if you want all debugging statements to print.  Certainly
 * not suitable for production.
 */
define("OS_DEBUG", False);

/**
 * Logging functionality for debugging library methods.  This only outputs 
 * data if OS_DEBUG is set to True or the override parameter is set.
 * @param string $label The label to print.
 * @param object $data An object to output as a string using print_r.
 * @param boolean $override Set this to true to print this output even if 
 *     OS_DEBUG is set to False.
 */
function OSLOG($label, $data, $override=False) {
  if (OS_DEBUG || $override) {
    $line = str_repeat("=", strlen($label) + 1);
    print(sprintf("\n%s:\n%s\n%s\n", $label, $line, print_r($data, True)));
  }
}

require_once("Zend/Json.php");
require_once("OAuth/OAuth.php");
require_once("OpenSocialHttpRequest.php");
require_once('OpenSocialRequest.php');
require_once("OpenSocialHttpLib.php");
require_once("OpenSocialCollection.php");
require_once("OpenSocialPerson.php");

/**
 * Base exception class for OpenSocial client library errors.  Contains const
 * values defining standard error codes.
 * @package OpenSocial
 */
class OpenSocialException extends Exception {
  /**
   * There was a problem with the configuration of the client.
   */
  const INVALID_CONFIG = 1;
  const HTTPLIB_ERROR = 2;
}

/**
 * Client library helper for making OpenSocial requests.
 * @package OpenSocial
 */
class OpenSocial {
  private $signature_method;
  private $oauth_consumer;
  private $server_rest_base;
  private $server_rpc_base;
  private $httplib;

  /**
   * Initializes this client object with the supplied configuration.
   */
  public function __construct($config, $httplib=null, $cache=null) {
    if (isSet($httplib)) {
      $this->httplib = $httplib;              // Allow overriding httplib.
    } else if (function_exists('curl_init')) {
      $this->httplib = new CurlHttpLib();     // Use curl on compatible systems.
    } else {
      $this->httplib = new SocketHttpLib();   // Default to using raw sockets.
    }

    $this->server_rest_base = $this->cleanUrl($config["server_rest_base"]);
    $this->server_rpc_base = $this->cleanUrl($config["server_rpc_base"]);
    
    if (!isSet($this->server_rpc_base) && !isSet($this->server_rest_base)) {
      throw new OpenSocialException(
          "Neither REST nor RPC endpoint was configured",
          OpenSocialException::INVALID_CONFIG
      );
    }
    
    // TODO: Support more methods of signing requests.
    $this->signature_method = new OAuthSignatureMethod_HMAC_SHA1();

    // Initialize consumer info. including consumer key and secret.
    $this->oauth_consumer = new OAuthConsumer(
        $config["oauth_consumer_key"], 
        $config["oauth_consumer_secret"], 
        null
    );
  }
  
  /**
   * Returns a string suitable for being a base URL for this library.  Cleans
   * " ", "/", and "?" from the end of the url.
   * @param string $url The url to clean.
   * @return string A cleaned url.
   */
  private function cleanUrl($url=null) {
    if (isSet($url)) {
      if (substr($url, 0, 4) !== "http") {
        throw new OpenSocialException(
            "Endpoint URLs must be absolute",
            OpenSocialException::INVALID_CONFIG
        );
      }
      return rtrim($url, "/ ?");
    } else {
      return null;
    }
  }
  
  /**
   * Accepts a list of OpenSocialRequest objects and sends each to the 
   * configured container, attempting to use RPC by default, but falling back
   * to REST if RPC is not configured.
   * @param mixed $requests An array of OpenSocialRequest objects or a single
   *     OpenSocialRequest object.
   * @param boolean $use_rest An override to prevent default behavior.  Set this
   *     to True to use REST even if RPC is configured.
   * @return mixed If an array was passed in, an array of the parsed responses 
   *     from these requests.  Otherwise, if a single request was passed, the
   *     single response from the request.
   */
  public function request($requests, $use_rest=False) {
    if ($use_rest === False && isSet($this->server_rpc_base)) {
      return $this->sendRpcRequests($requests);
    } else {
      if (is_array($requests)) {
        return $this->sendRestRequests($requests); 
      } else {
        return $this->sendRestRequest($requests);
      }
    }
  }
  
  /**
   * Sends a request or set of requests using the RPC protocol.  
   * @param mixed $requests A single OpenSocialRequest or an array of 
   *     OpenSocialRequest objects.
   * @return mixed If an array was passed in, an array of the parsed responses 
   *     from these requests.  Otherwise, if a single request was passed, the
   *     single response from the request.
   */
  protected function sendRpcRequests($requests) {
    $body = array();
    $reqs = array();
    $requestor = null;
    // TODO: Refactor this a bit... too much overlapping code.
    if (is_array($requests)) {
      foreach ($requests as $request) {
        $body[] = $request->getRpcBody();
        $reqs[$request->getId()] = $request;
        $req_requestor = $request->getRequestor();
        if (isSet($req_requestor)) {
          $requestor = $req_requestor;
        }
      }      
    } else {
      $body[] = $requests->getRpcBody();
      $reqs[$requests->getId()] = $requests;
      $req_requestor = $requests->getRequestor();
      if (isSet($req_requestor)) {
        $requestor = $req_requestor;
      }
    }
    $http_request = new OpenSocialHttpRequest(
        "POST", 
        $this->server_rpc_base, 
        null,   
        $body
    );
    if (isSet($requestor)) {
      $http_request->setRequestor($requestor);
    }
    $http_request->sign($this->oauth_consumer, $this->signature_method);
    $http_result = $this->httplib->sendRequest($http_request);
    $text_result = $http_result->getText();
    
    $ret = array();
    $json_result = Zend_Json::decode($text_result);
    
    foreach ($json_result as $response) {
      $id = $response["id"];
      $data = $reqs[$id]->processJsonResponse($response["data"], "RPC");
      if (is_array($requests)) {
        $ret[$id] = $data;
      } else {
        return $data;
      }
    }
    return $ret;
  }
  
  /**
   * Sends an array of requests to the configured container.
   * @param array An array of OpenSocialRequest objects.
   * @return array A map of the request ID number and the parsed responses
   * from the container.
   */
  protected function sendRestRequests($requests) {
    $results = array();
    foreach ($requests as $request) {
      $results[$request->getId()] = $this->sendRestRequest($request);
    }
    return $results;
  }
  
  /**
   * Sends a single REST request to the configured container and returns the
   * parsed response.  Before sending the request to the container, this method
   * signs the request with the configured credentials.
   * @param mixed $request The OpenSocialRequest object to send.
   * @return mixed The parsed response of the request.
   */
  protected function sendRestRequest($request) {
    // If you want to sign a request using a security token, you can use
    // $http_request->signWithToken("<token>"); although this is not part
    // of the spec.
    $http_request = $request->getRestRequest($this->server_rest_base);
    $http_request->sign($this->oauth_consumer, $this->signature_method);
    $http_result = $this->httplib->sendRequest($http_request);
    $text_result = $http_result->getText();
    $json_result = Zend_Json::decode($text_result);
    $result = $request->processJsonResponse($json_result, "REST");
    return $result;
  }
  
  /**
   * Fetches a single person.
   */
  public function fetchPerson($guid, $fields = Array()) {
    $req = new FetchPersonRequest($guid);
    return $this->request($req);
  }
  
  /**
   * Fetches the friends of the specified user.
   */
  public function fetchFriends($guid, $fields = Array()) {
    $req = new FetchPeopleRequest($guid, "@friends");
    return $this->request($req);
  }
}

?>
