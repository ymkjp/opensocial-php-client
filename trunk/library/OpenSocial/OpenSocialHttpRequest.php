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
 * Library requires our modified OAuth library and the Zend JSON library.
 */
require_once("OAuth/OAuth.php");
require_once("Zend/Json.php");

/**
 * Abstracts a request object to be sent to the OpenSocialHttpLib class.
 * @package OpenSocial
 */
class OpenSocialHttpRequest {
  private $oauth_request;
  private $body;
  private $is_signed;
  private $consumer;
  private $requestor;
  
  /**
   * Creates a request to be sent to an OpenSocial server.   
   * @param string $method The HTTP method to use for this request.
   * @param string $url The URL for this request.  Do not include querystring
   *     parameters in this url because they will likely not be included 
   *     correctly when signing this request.
   * @param array $signed_params An array of strings representing querystring
   *     parameters that will be added to the url.  These parameters will be
   *     included when signing the request.
   * @param body $string The value of the request body.  This will not be 
   *     included when signing the request.
   */
  public function __construct($method, $url, $signed_params=null, $body=null) {
    $is_signed = False;
    $this->body = $body;
    if (!$signed_params) {
      // So the client library won't initialize oauth_request if $method is not
      // GET or POST and $signed_params is empty - so let's insert some junk
      // data into the request to get around this for the time being :P
      $signed_params = array("opensocial_method" => $method);
    }
    $this->oauth_request = OAuthRequest::from_request(
        $method,
        $url,
        $signed_params
    );
  }
  
  /**
   * Sets the specified parameter for this request.  
   *
   * The parameter will be included in the signature if this request is signed, 
   * and will be passed as a query parameter regardless of the HTTP method used.
   * @param string $name The name of the parameter.
   * @param string $value The value of the parameter.
   */
  public function setParameter($name, $value) {
    // TODO: Check is_signed and throw exception if the request is signed
    $this->oauth_request->set_parameter($name, $value);
  }
  
  /**
   * Sets multiple parameters for this request.
   * @param array $parameters An array of key value string pairs to assign
   *     as parameters.
   */
  public function setParameters($parameters) {
    foreach ($parameters as $name => $value) {
      $this->setParameter($name, $value);
    }
  }
  
  /**
   * For the two-legged OAuth case, some requests need a viewer context.  This
   * method assigns the appropriate VIEWER id to the xoauth_requestor_id
   * OAuth parameter.
   * @param string $id ID of the VIEWER.
   */
  public function setRequestor($id) {
    $this->requestor = $id;
  }
  
  /**
   * Returns the value of the specified parameter.  Used for unit testing.
   * @param string $name The name of the parameter to retrieve.
   * @return string The value of the parameter.
   */
  public function getParameter($name) {
    return $this->oauth_request->get_parameter($name);
  }
  
  /**
   * Signs the current request with the supplied credentials.
   * @param mixed $consumer The OAuthConsumer credential object.
   * @param mixed $signature_method The OAuthSignatureMethod object indicating
   *     which encryption method to use when signing this request.
   */
  public function sign($consumer, $signature_method) {
    // Simple nonce.  Not doing this in the OAuth library because they insist
    // on getting the consumer at construction which I want to avoid.
    $nonce = md5(microtime() . mt_rand());
    
    // TODO: See if there's a case for supporting oauth_token
    $parameters = array(
      "oauth_nonce" => $nonce,
      "oauth_version" => OAuthRequest::$version,
      "oauth_timestamp" => time(),
      "oauth_consumer_key" => $consumer->key
    );
    
    // Add requestor data if it exists.
    if (isSet($this->requestor)) {
      $parameters["xoauth_requestor_id"] = $this->requestor;
    }
    
    // Ugly hack because implementations currently need the body to be signed.
    $body = $this->getBody();
    if (isSet($body)) {
      $parameters[$body] = null;
    }
    
    $this->setParameters($parameters);
    $this->oauth_request->sign_request($signature_method, $consumer, null);
    $this->is_signed = True;
    
    // Undo the ugly hack by removing the body value from the querystring.
    // TODO: Make oauth_request->parameters private once this is fixed.
    if (isSet($body)) {
      unset($this->oauth_request->parameters[$body]);
    }
  }
  
  /**
   * Allows signing a request with a security token instead of OAuth.
   * @param string $token The security token to use.
   */
  public function signWithToken($token) {
    $this->setParameter("st", $token);
    $this->is_signed = True;
  }
  
  /**
   * Returns a string indicating this request's HTTP method.
   * @return string One of "GET", "PUT", "POST", "DELETE", etc.
   */
  public function getMethod() {
    return $this->oauth_request->get_normalized_http_method();
  }
  
  /** 
   * Returns the url of the current request.
   * @return string The request url.
   */
  public function getUrl() {
    // Put all the signed parameters into the URL because we're not doing
    // application/x-www-form-urlencoded POST bodies.
    return $this->oauth_request->to_url();
  }
  
  /**
   * Returns a normalized URL without querystring parameters for the current 
   * request.  This is to be mostly used for unit testing - if you want to get
   * an actual url to request, use the getUrl() method instead.
   * @return string A normalized URL without query parameters.
   */
  public function getNormalizedUrl() {
    return $this->oauth_request->get_normalized_http_url();
  }
  
  /**
   * Returns the body of the current request or null if this is a GET.
   * @return string The request body or null.
   */
  public function getBody() {
    // Return the supplied body code (not signed).
    if (!isSet($this->body)) {
      return null;
    } else if (is_string($this->body)) {
      return $this->body;
    } else {
      // TODO: This feels like it's in the wrong place.
      return Zend_Json::encode($this->body);
    }
  }
  
  /**
   * Returns an array of headers for this request.
   * @return array An array of header strings, one header per string.
   */
  public function getHeaders() {
    // TODO: Consider making headers work more like parameters, with setHeader.
    $headers = array();
    if ($this->getMethod() != "GET") {
      $headers[] = sprintf("Content-length: %s", strlen($this->getBody()));
    }
    
    // TODO: Only set this if we know for sure the body is JSON.
    if ($this->getBody() != null) {
      $headers[] = "Content-type: application/json";
    }
    
    return $headers;
  }
}