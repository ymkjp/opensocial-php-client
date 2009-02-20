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
 * The osapiLocalHttpProvider class is used to define a mock HTTP layer that
 * returns a pre-defined response.
 *
 * @author Dan Holevoet
 */
class osapiLocalHttpProvider extends osapiHttpProvider {
  protected $response;
  protected $storage;
  
  public function __construct($response, $storage = null) {
    $this->response = $response;
    $this->storage = $storage;
  }
  
  /**
   * Sends a request using the supplied parameters.
   *
   * @param string $url the requested URL
   * @param string $method the HTTP verb to use
   * @param string $postBody the optional POST body to send in the request
   * @param boolean $headers whether or not to return header information
   * @param string $ua the user agent to send in the request
   * @return array the returned data and status code
   */
  public function send($url, $method, $postBody = false, $headers = false, $ua = self::USER_AGENT) {
    if ($this->storage) {
      $this->storage->set("body", $postBody);
    }
    $nextResponse = array_pop($this->response);
    return array('http_code' => 200, 'data' => $nextResponse);
  }
  
  /**
   * Sets the array of fake responses.
   *
   * @param array $response the fake responses
   */
  public function setResponse($response) {
    $this->response = $response;
  }
  
  /**
   * Sets the local storage provider for logging outgoing requests.
   *
   * @param osapiStorage $storage the local storage
   */
  public function setStorage($storage) {
    $this->storage = $storage;
  }
}
