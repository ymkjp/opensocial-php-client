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
 
require_once("OAuth/OAuth.php");

/**
 * Abstracts a request object to be sent to the OpenSocialHttpLib class.
 */
class OpenSocialHttpRequest {
  private $oauth_request;
  private $body;
  private $is_signed;
  private $consumer;
  
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
    $this->setParameter("oauth_version", OAuthRequest::$version);
    $this->setParameter("oauth_nonce", $nonce);
    $this->setParameter("oauth_timestamp", time());
    $this->setParameter("oauth_consumer_key", $consumer->key);
    
    $this->oauth_request->sign_request($signature_method, $consumer, null);
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
   * Returns the body of the current request or null if this is a GET.
   * @return string The request body or null.
   */
  public function getBody() {
    // Return the supplied body code (not signed).
    return $this->body;
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
    return $headers;
  }
}