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

interface OpenSocialHttpLib {
  public function send_request($oauth_request);
}

class SocketHttpLib implements OpenSocialHttpLib {
  private static $user_agent = "OpenSocial API Client (socket)";
  
  public function send_request($oauth_request) {
    // Determine whether this is a GET or a POST, PUT, or DELETE 
    if ($oauth_request->get_normalized_http_method() == "GET") {
      $url = $oauth_request->to_url();
      $body = null;
    } else {
      $url = $oauth_request->get_normalized_http_url();
      $body = $oauth_request->to_postdata();
    }
  
    // Define the headers for the request.
    $headers = array(
        "Content-type: application/x-www-form-urlencoded",
        sprintf("User-Agent: %s", $user_agent),
        sprintf("Content-length: %s", strlen($body))
    );
    
    $context = array(
        "http" => array(
            "method" => $method, 
            "header" => implode("\r\n", $headers),
            "content" => $body
        )
    );
    
    $context_id = stream_context_create($context);
    $socket = fopen($url, "r", false, $context_id);
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

/*
class OpenSocialHttpLib { 
//  public $oauth_consumer_key;
//  public $oauth_consumer_secret;
  
  public function __construct() {
  }
  

  public function OpenSocialHttpLib($oauth_consumer_key, $oauth_consumer_secret) {
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

    if ( function_exists('curl_init')) {
      // Use CURL if installed...
      return $this->send_curl($post_url);
    } else {
      // Non-CURL based version...
      return $this->send_socket("GET", $post_url);
    }
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

    // TODO: Split this out into proper OOP
    if ( function_exists('curl_init')) {
      // Use CURL if installed...
      return $this->send_curl($post_url, $json_body);
    } else {
      // Non-CURL based version...
      return $this->send_socket("POST", $post_url, $json_body);
    }
  }
  
  function send_curl($url, $body=null) {
    //TODO: Move this out into a class property
    $user_agent = sprintf("OpenSocial API Client (curl) %s", phpversion());
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if (isSet($body)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
  }
  
  function send_socket($method, $url, $body="") {
    $user_agent = sprintf("OpenSocial API Client (socket) %s", phpversion());
    $headers = array(
        "Content-type: application/x-www-form-urlencoded",
        sprintf("User-Agent: %s", $user_agent),
        sprintf("Content-length: %s", strlen($body))
    );
    $context = array(
        "http" => array(
            "method" => $method, 
            "header" => implode("\r\n", $headers),
            "content" => $body
        )
    );
    
    $context_id=stream_context_create($context);
    $sock=fopen($url, "r", false, $context_id);
    if ($sock) {
      $result="";
      while (!feof($sock)) {
        $result .= fgets($sock, 4096);
      }
      fclose($sock);
    }
    
    return $result;
  }

  public function get_signable_parameters_x($parameters, $json_body) {
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
  }
}
*/
?>
