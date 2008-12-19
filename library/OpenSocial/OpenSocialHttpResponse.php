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
 
class OpenSocialHttpResponse {
  private $http_status;
  private $headers;
  private $text;
  
  public function __construct($http_status, $headers=null, $text) {
    $this->http_status = $http_status;
    $this->headers = $headers;
    $this->text = $text;
  }
  
  public function getHttpStatus() {
    return $this->http_status;
  }
  
  public function getHeaders() {
    return $this->headers;
  }
  
  public function getText() {
    return $this->text;
  }
}