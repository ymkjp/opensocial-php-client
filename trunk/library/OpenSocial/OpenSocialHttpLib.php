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
 * Creates an interface through which clients may define classes to use custom
 * HTTP libraries.
 */
interface OpenSocialHttpLib {
  /**
   * Queries the specified server and returns the response as text.
   * @param mixed $oauth_request An OAuthRequest object that should be signed.
   * @return string The text returned by the server.
   */
  public function sendRequest($oauth_request);
}

/**
 * An implementation of the OpenSocialHttpLib interface that uses raw sockets.
 * This implementation should be widely compatible but is unsophisticated.
 */
class SocketHttpLib implements OpenSocialHttpLib {
  /**
   * The value sent as the User-Agent header when this makes a request.
   */
  const USER_AGENT = "OpenSocial API Client (socket)";
  
  /**
   * Queries the specified server and returns the response as text.
   * @param mixed $request An OpenSocialHttpRequest object.
   * @return string The text returned by the server.
   */
  public function sendRequest($request) {
    // Get the headers for the request.
    $headers = $request->getHeaders();
    $headers[] = sprintf("User-Agent: %s", self::USER_AGENT);
    
    $context = array(
        "http" => array(
            "method" => $request->getMethod(), 
            "header" => implode("\r\n", $headers),
            "content" => $request->getBody()
        )
    );
    
    $context_id = stream_context_create($context);
    $socket = fopen($request->getUrl(), "r", false, $context_id);
    if ($socket) {
      $result = "";
      while (!feof($socket)) {
        $result .= fgets($socket, 4096);
      }
      // TODO: Determine whether this should be in a try/catch/finally block
      fclose($socket);
    }
    
    return $result;
  }
}


/**
 * An implementation of the OpenSocialHttpLib interface that uses curl.
 * This implementation should be more robust than the sockets implementation
 * but requires curl to be built into PHP.
 */
class CurlHttpLib implements OpenSocialHttpLib {
  /**
   * The value sent as the User-Agent header when this makes a request.
   */
  const USER_AGENT = "OpenSocial API Client (curl)";
  
  /**
   * Queries the specified server and returns the response as text.
   * @param mixed $request An OpenSocialHttpRequest object.
   * @return string The text returned by the server.
   */
  public function sendRequest($request) {    
    // Configure the curl parameters.
    $url = $request->getUrl();
    $body = $request->getBody();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($request->getMethod() != "GET") {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
    
    // Send the curl request.
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
  }
}
?>
