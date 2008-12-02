<?php
/*
 * +--------------------------------------------------------------------------+
 * | OpenSocial PHP5 client                                           |
 * +--------------------------------------------------------------------------+
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 */


class OpenSocialHttpLib {
  public $server_addr; 
  public $oauth_consumer_key;
  public $oauth_consumer_secret;

  /**
   * Create the client.
   */
  public function OpenSocialHttpLib($server_addr, $oauth_consumer_key, $oauth_consumer_secret) {
     $this->server_addr  = $server_addr; 
     $this->oauth_consumer_key = $oauth_consumer_key;
     $this->oauth_consumer_secret = $oauth_consumer_secret;
  }

  public function send_request($rest_endpoint, $params) {
    global $user;

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

    //Build a request object from the current request
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

    // Initialize consumer info. including consumer key and secret
    $consumer = new OAuthConsumer($this->oauth_consumer_key, $this->oauth_consumer_secret, null);

    //Build a request object from the current request
    $request = OAuthRequest::from_consumer_and_token($consumer, null, "GET", $rest_endpoint, array('xoauth_requestor_id' => $user) );

    $request->sign_request($signature_method, $consumer,null);

    //echo "BASE: " . $request->base_string . "<BR>\n";

    $post_url = $rest_endpoint . '?' . $request->get_signable_parameters() . '&oauth_signature=' .  OAuthUtil::urlencodeRFC3986($request->get_parameter("oauth_signature"));
    $post_string = "";

    if ($GLOBALS['opensocial_config']['debug']) {
        echo "POST: " . $post_url;
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
              array('method' => 'GET',
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
    global $user;

    //Build a request object from the current request
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

    // Initialize consumer info. including consumer key and secret
    $consumer = new OAuthConsumer($this->oauth_consumer_key, $this->oauth_consumer_secret, null);

    //Build a request object from the current request
    $request = OAuthRequest::from_consumer_and_token($consumer, null, "POST", $rpc_endpoint, array('xoauth_requestor_id' => $user, $json_body => '') );

    $request->sign_request($signature_method, $consumer,null);

    //echo "BASE: " . $request->base_string . "<BR>\n";
    //exit;

    $post_url = $rpc_endpoint . '?' . $this->get_signable_parameters_x($request->get_parameters(), $json_body) . '&oauth_signature=' .  OAuthUtil::urlencodeRFC3986($request->get_parameter("oauth_signature"));

    if ( function_exists('curl_init')) {
      // Use CURL if installed...
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $post_url);
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
                    'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                                'Content-length: ' . strlen($json_body),
                    'content' => $json_body));
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


  public function get_signable_parameters_x($parameters, $json_body) {/*{{{*/
    // Grab all parameters
    $params = $parameters;

    // Remove oauth_signature if present
    if (isset($params['oauth_signature'])) {
      unset($params['oauth_signature']);
    }

    // Remove json_body if present
    if (isset($params[$json_body])) {
      unset($params[$json_body]);
    }

    // Urlencode both keys and values
    $keys = array_map(array('OAuthUtil', 'urlencodeRFC3986'), array_keys($params));
    $values = array_map(array('OAuthUtil', 'urlencodeRFC3986'), array_values($params));
    $params = array_combine($keys, $values);

    // Sort by keys (natsort)
    uksort($params, 'strnatcmp');

    // Generate key=value pairs
    $pairs = array();
    foreach ($params as $key=>$value ) {
      if (is_array($value)) {
        // If the value is an array, it's because there are multiple
        // with the same key, sort them, then add all the pairs
        natsort($value);
        foreach ($value as $v2) {
          $pairs[] = $key . '=' . $v2;
        }
      } else {
        $pairs[] = $key . '=' . $value;
      }
    }

    // Return the pairs, concated with &
    return implode('&', $pairs);
  }/*}}}*/
}

?>
