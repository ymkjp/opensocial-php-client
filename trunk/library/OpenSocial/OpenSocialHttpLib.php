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

require_once "OpenSocialHttpResponse.php";

/**
 * Creates an interface through which clients may define classes to use custom
 * HTTP libraries.
 * @package OpenSocial
 */
interface OpenSocialHttpLib {

  /**
   * Queries the specified server and returns the response as text.
   * @param mixed $oauth_request An OAuthRequest object that should be signed.
   * @return OpenSocialHttpResponse A response object.
   */
  public function sendRequest($oauth_request);
}

/**
 * An implementation of the OpenSocialHttpLib interface that uses raw sockets.
 * This implementation should be widely compatible but is unsophisticated.
 * @package OpenSocial
 */
class SocketHttpLib implements OpenSocialHttpLib {
  private $http_status;
  
  /**
   * The value sent as the User-Agent header when this makes a request.
   */
  const USER_AGENT = "OpenSocial API Client (socket)";

  public function onRequestNotification($notification_code, $severity, $message, $message_code, $bytes_transferred, $bytes_max) {
    
    switch ($notification_code) {
      case STREAM_NOTIFY_FAILURE:
        if (isSet($message_code)) {
          $this->http_status = (string)$message_code;
        }
        break;
      case STREAM_NOTIFY_CONNECT:
        $this->http_status = "200";
        break;
    }
  }

  /**
   * Queries the specified server and returns the response as text.
   * @param mixed $request An OpenSocialHttpRequest object.
   * @return OpenSocialHttpResponse A response object.
   */
  public function sendRequest($request) {
    // Get the headers for the request.
    $headers = $request->getHeaders();
    $headers[] = sprintf("User-Agent: %s", self::USER_AGENT);
    
    $context = array(
        "http" => array("method" => $request->getMethod(), 
            "header" => implode("\r\n", $headers), 
            "content" => $request->getBody()));
    
    $context_id = stream_context_create($context);
    
    $this->http_status = null;
    stream_context_set_params($context_id, array("notification" => array($this, "onRequestNotification")));
    
    try {
      $stream = fopen($request->getUrl(), "r", false, $context_id);
    } catch (Exception $e) {
      if (! isSet($this->http_status)) {
        throw new OpenSocialException(sprintf("Socket error: %s [%s]", $e->getMessage(), $e->getCode()), OpenSocialException::HTTPLIB_ERROR);
      }
    }
    
    if ($stream) {
      $result = "";
      while (! feof($stream)) {
        $result .= fgets($stream, 4096);
      }
      
      // TODO: This should check for a problem and throw an exception
      $metadata = stream_get_meta_data($stream);
      $http_wrapper_data = $metadata["wrapper_data"];
      $http_status_line = $http_wrapper_data[0];
      $http_status_parts = explode(" ", $http_status_line);
      $this->http_status = $http_status_parts[1];
      
      // TODO: Determine whether this should be in a try/catch/finally block
      fclose($stream);
    }
    
    return new OpenSocialHttpResponse($this->http_status, null, $result);
  }
}

/**
 * An implementation of the OpenSocialHttpLib interface that uses curl.
 * This implementation should be more robust than the sockets implementation
 * but requires curl to be built into PHP.
 * @package OpenSocial
 */
class CurlHttpLib implements OpenSocialHttpLib {
  /**
   * The value sent as the User-Agent header when this makes a request.
   */
  const USER_AGENT = "OpenSocial API Client (curl)";

  /**
   * Queries the specified server and returns the response as text.
   * @param mixed $request An OpenSocialHttpRequest object.
   * @return OpenSocialHttpResponse A response object.
   */
  public function sendRequest($request) {
    OSLOG("CurlHttpLib::sendRequest - request->getMethod()", $request->getMethod());
    OSLOG("CurlHttpLib::sendRequest - request->getUrl()", $request->getUrl());
    OSLOG("CurlHttpLib::sendRequest - request->getBody()", $request->getBody());
    OSLOG("CurlHttpLib::sendRequest - request", $request);
    
    // Configure the curl parameters.
    $url = $request->getUrl();
    $body = $request->getBody();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($request->getMethod() != "GET") {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());
    } else {
      curl_setopt($ch, CURLOPT_HTTPGET, 1);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
    
    // Send the curl request.
    $result = curl_exec($ch);
    $info = curl_getinfo($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($errno != CURLE_OK) {
      throw new OpenSocialException("Curl error: " . $error, OpenSocialException::HTTPLIB_ERROR);
    }
    
    $response_obj = new OpenSocialHttpResponse($info["http_code"], null, $result);
    OSLOG("CurlHttpLib::sendRequest - response_obj", $response_obj);
    return $response_obj;
  }
}
