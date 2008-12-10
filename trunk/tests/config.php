<?php
/*
 * +--------------------------------------------------------------------------+
 * | OpenSocial PHP5 client                                           |
 * +--------------------------------------------------------------------------+
 * Copyright (c) 2008 Google Inc.
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

require_once("../client/OAuth.php");

$oauth_consumer_key = 'orkut.com:623061448914';
$oauth_consumer_secret = 'uynAeXiWTisflWX99KU1D2q5';

if( isset($_GET['debug']) && $_GET['debug'] == 1 ) {
    $opensocial_config['debug'] = true;
}
else {
    $opensocial_config['debug'] = false;
}

// Temporary user id for orkut
// Until 2-legged oauth is ready to authenticate
$user = '04996716008119675151';

// Create instance 
$opensocial = new OpenSocial($oauth_consumer_key, $oauth_consumer_secret);

