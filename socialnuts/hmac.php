<?php
/* Copyright (c) 2008 Google Inc.
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

  require_once("../library/OAuth.php");
 
  if (verifySignature()) {
    print ('Validated HMAC!');
  } else {
    print ('Spoofed HMAC');
  }

  function verifySignature() {
    //Build a request object from the current request
    $request = OAuthRequest::from_request(null, null, array_merge($_GET, $_POST));

    // Initialize consumer info. including consumer key and secret
    $consumer = new OAuthConsumer('524773807507', 'zlvVTl9uTjOv6zJGH0uvta7m', null);

    //Initialize the new signature method
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
 
    //Check the request signature
    @$signature_valid = $signature_method->check_signature($request, $consumer, null, $_GET["oauth_signature"]);

    return $signature_valid;
  }
?>
