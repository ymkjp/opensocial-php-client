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
 * An implementation of the OpenSocialHttpLib interface that can be used for
 * unit testing.
 */
class MockHttpLib implements OpenSocialHttpLib {
  private $response;
  private $request;
  
  /**
   * Sets the text that this mock will return when sendRequest is called.
   * @param string $response The text to return.
   */
  public function setResponse($response) {
    $this->response = $response;
  }
  
  /**
   * Returns the last request that was sent to this library.
   * @return OAuthRequest The last request that was sent to this library.
   */
  public function getRequest() {
    return $this->request;
  }
  
  /**
   * Queries the specified server and returns the response as text.
   * @param mixed $oauth_request An OAuthRequest object that should be signed.
   * @return string The text returned by the server.
   */
  public function sendRequest($oauth_request) {
    $this->request = $oauth_request;
    if (!isSet($this->response)) {
      $this->response = new OpenSocialHttpResponse("200", null, null);
    }
    return $this->response;
  }
}