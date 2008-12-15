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
require_once("OAuth/OAuth.php");
require_once("OpenSocialHttpRequest.php");
require_once('OpenSocialRequest.php');
require_once("OpenSocialHttpLib.php");
require_once("OpenSocialCollection.php");
require_once("OpenSocialPerson.php");

/**
 * Client library helper for making OpenSocial requests.
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
      $this->httplib = $httplib;          // Allow overriding default httplibs.
    } else if (function_exists('curl_init')) {
      $this->httplib = new CurlHttpLib();       // Use curl on compatible systems.
    } else {
      $this->httplib = new SocketHttpLib();     // Default to using raw sockets.
    }

    $this->server_rest_base = $this->cleanUrl($config["server_rest_base"]);
    $this->server_rpc_base = $this->cleanUrl($config["server_rpc_base"]);
    
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
    // TODO: Throw a configuration exception if this doesn't start with http://
    // or isn't a valid url in some other way.
    if (isSet($url)) {
      return rtrim($url, "/ ?");
    } else {
      return null;
    }
  }
  
  /**
   * Accepts a list of OpenSocialRequest objects and sends each to the 
   * configured container, attempting to use RPC by default, but falling back
   * to REST if RPC is not configured.
   * @param mixed $requests An array of OpenSocialRequest objects.
   * @param boolean $use_rest An override to prevent default behavior.  Set this
   *     to True to use REST even if RPC is configured.
   * @return array An array of the parsed responses from these requests.
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
   * 
   */
  protected function sendRpcRequests($requests) {
    return array();
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
    $http_request = $request->getRestRequest($this->server_rest_base);
    $http_request->sign($this->oauth_consumer, $this->signature_method);
    $text_result = $this->httplib->sendRequest($http_request);
    $json_result = Zend_Json::decode($text_result);
    $result = $request->processJsonResponse($json_result);
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
