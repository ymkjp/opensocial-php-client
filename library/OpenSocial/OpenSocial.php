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


include_once 'OpenSocialClient.php';

class OpenSocial {
  public $os_client;

  public $oauth_consumer_key;
  public $oauth_consumer_secret;

  public $os_params;
  public $user;

  /**
   * Create the object
   * @param     string   $oauth_consumer_key   
   * @param     string   $oauth_consumer_secret   
   */
  public function OpenSocial($oauth_consumer_key, $oauth_consumer_secret) {
    $this->oauth_consumer_key    = $oauth_consumer_key;
    $this->oauth_consumer_secret = $oauth_consumer_secret;

    $this->os_client = new OpenSocialClient($oauth_consumer_key, $oauth_consumer_secret);
  }

  /**
   * set current user
   * @param     string   $user   
   * @param     string   $session_key   
   */
  public function set_user($user, $session_key, $expires=null) {
    $this->user = $user;
    $this->os_client->session_key = $session_key;
  }

  /**
   * get current user
   */
  public function get_current_user() {
    return $this->user;
  }

  /**
   * 
   */
  public static function current_url() {
    return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }

  /**
   * 
   */
  public static function get_container_url($subdomain='www') {
    return 'http://' . $subdomain . '.orkut.com/social/rest/';
  }

  /**
   * Handle magic quotes
   * @param     string   $v   
   * @return    string   v without added slashes
   */
  public static function no_magic_quotes($v) {
    if (get_magic_quotes_gpc()) {
      return stripslashes($v);
    } else {
      return $v;
    }
  }
}

?>
