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
 * Pre-defined provider class for Orkut (www.orkut.com)
 * Note: Orkut currently only supports the SecurityToken and
 * 2-legged OAuth auth methods, and it doesn't support the
 * activities end-point.
 * @author Chris Chabot
 */
class osapiOrkutProvider extends osapiProvider {

  /**
   * Specifies the appropriate data for an orkut request.
   * @param osapiHttpProvider httpProvider The HTTP request provider to use.
   */
  public function __construct(osapiHttpProvider $httpProvider = null) {
    parent::__construct(null, null, null, 'http://sandbox.orkut.com/social/rest/', 'http://sandbox.orkut.com/social/rpc', "Orkut", true, $httpProvider);
  }

  /**
   * Adjusts a request prior to being sent in order to fix orkut-specific bugs.
   * @param mixed $request The osapiRequest object being processed, or an array
   *     of osapiRequest objects.
   * @param string $method The HTTP method used for this request.
   * @param string $url The url being fetched for this request.
   * @param array $headers The headers being sent in this request.
   */
  public function preRequestProcess(&$request, &$method, &$url, &$headers) {
    $this->fixContentType($headers);
    
    if (is_array($request)) {
      foreach ($request as $req) {
        $this->fixRequest($req);
      }
    } else {
      $this->fixRequest($request);
    }
  }

  /**
   * Attempts to correct an atomic orkut request.
   * @param osapiRequest $request The request to fix.
   */
  private function fixRequest(osapiRequest &$request) {
    $this->fixViewer($request);
    $this->fixFields($request);
  }

  /**
   * Fixes the "invalid signature" error when using application/json.
   * @param array $headers The headers for the given request.
   */
  private function fixContentType(&$headers) {
    if ($headers && is_array($headers)) {
      $key = array_search("Content-Type: application/json", $headers);
      if ($key !== false) {
        unset($headers[$key]);
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
      }
    }
  }

  /**
   * Fixes the "non partial app updates are not implemented yet error.
   * @param osapiRequest $request The request to adjust.
   */
  public function fixFields(osapiRequest &$request) {
    if ($request->method == 'appdata.create' ||
        $request->method == 'appdata.update') {
      $request->params['fields'] = array_keys($request->params['data']);
    }
  }

  /**
   * Fixes the "forbidden: Only app data for the viewer can be modified" error.
   * @param osapiRequest $request The request to adjust.
   */
  public function fixViewer(osapiRequest &$request) {
    if ($request->method == 'appdata.create' ||
        $request->method == 'appdata.update') {
      foreach ($request->params['userId'] as $key => $value) {
        if ($value === "@me") {
          $request->params['userId'][$key] = "@viewer";
        }
      }
    }
  }
}
