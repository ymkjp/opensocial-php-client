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

class OpenSocialHttpLib {
  public $server_addr; 
  /**
   * Create the client.
   */
  public function OpenSocialHttpLib($server_addr) {
     $this->server_addr  = $server_addr; 
  }

  public function send_request($rest_endpoint, $params) {
    global $token;
    $post_params = array();
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
      $get_params[] = $key.'='.$val;
      //$get_params[] = $key.'='.urlencode($val);
    }
    if( !empty($get_params) ) {
        //var_dump($get_params);
        $post_string = implode('&', $get_params);
    }
    $post_url = $rest_endpoint;

    $post_url .= '?' . $token; 
    //$post_url = $rest_endpoint . "?" . $get_string;
    
    if ($GLOBALS['opensocial_config']['debug']) {
        //echo "POST: " . $post_url . "?" . $post_string;
    }

    if ( function_exists('curl_init')) {
      // Use CURL if installed...
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $post_url);
      //curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, 'OpenSocial API PHP5 Client 1.0 (curl) ' . phpversion());
      $result = curl_exec($ch);
      curl_close($ch);
    } else {
      // Non-CURL based version...
      $context =
        array('http' =>
              array('method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                                'User-Agent: OpenSocial API PHP5 Client 1.0 (non-curl) '.phpversion()."\r\n".
                                'Content-length: ' . strlen($post_string),
                    'content' => $post_string));
      $contextid=stream_context_create($context);

      $sock=fopen($post_url, 'r', false, $contextid);
      if ($sock) {
        $result='';
        while (!feof($sock))
          $result.=fgets($sock, 4096);

        fclose($sock);
      }
    }
    return $result;
  }

  public function send_rpc_request($rpc_endpoint, $json_body) {
    global $token;

    $rpc_endpoint .= "?$token";

    if ( function_exists('curl_init')) {
      // Use CURL if installed...
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $rpc_endpoint);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, 'OpenSocial API PHP5 Client 1.0 (curl) ' . phpversion());
      $result = curl_exec($ch);
      curl_close($ch);
    } else {
      // Non-CURL based version...
      $context =
        array('http' =>
              array('method' => 'POST',
                    'header' => 'Content-type: application/json'."\r\n".
                                'Authorization: '."$token\r\n",
                    'content' => $json_body));
      $contextid=stream_context_create($context);

      $sock=fopen($rpc_endpoint, 'r', false, $contextid);
      if ($sock) {
        $result='';
        while (!feof($sock))
          $result.=fgets($sock, 4096);

        fclose($sock);
      }
    }
    return $result;
  }

}

?>
