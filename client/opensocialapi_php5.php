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

require_once("../Zend/Json.php");

include_once 'opensocial_php5_httplib.php';

class OpenSocialClient {
  public $session_key;
  public $oauth_consumer_key;
  public $oauth_consumer_secret;

  /**
   * Create the client.
   * @param string $session_key 
   */
  public function OpenSocialClient($oauth_consumer_key, $oauth_consumer_secret, $session_key=null) {
    $this->session_key  = $session_key;
    $this->oauth_consumer_key      = $oauth_consumer_key;
    $this->oauth_consumer_secret   = $oauth_consumer_secret;
    $this->server_addr  = OpenSocial::get_container_url('sandbox');


    if ($GLOBALS['opensocial_config']['debug']) {
      $this->cursor = 0;
      ?>
<script type="text/javascript">
var types = ['params', 'xml', 'php', 'sxml'];
function toggleDisplay(id, type) {
  for each (var t in types) {
    if (t != type || document.getElementById(t + id).style.display == 'block') {
      document.getElementById(t + id).style.display = 'none';
    } else {
      document.getElementById(t + id).style.display = 'block';
    }
  }
  return false;
}
</script>
<?php
    }

  }

  /**
   * Returns the session information
   * @param string $auth_token the token returned by auth_createToken or
   *  passed back to your callback_url.
   * @return assoc array containing session_key, uid
   */
  public function auth_getSession() {
    $hi5apiserver = "http://api.hi5.com/rest/auth/plain/";
    $httplib = new OpenSocialHttpLib($hi5apiserver, $this->oauth_consumer_key, $oauth_consumer_secret);

    $params = array('username' => '', 'password' => '', 'oauth_consumer_key' => '');

    $post_params = array();
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
      $get_params[] = $key.'='.$val;
      //$get_params[] = $key.'='.urlencode($val);
    }
    //var_dump($get_params);
    $post_string = implode('&', $get_params);
    $post_url = $hi5apiserver;  
   
    //echo "POST: " . $post_url . ":" . $post_string;

    if (function_exists('curl_init')) {
      // Use CURL if installed...
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $post_url);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, 'OpenSocial API PHP5 Client 1.0 (curl) ' . phpversion());
      $result = curl_exec($ch);
      curl_close($ch);
    }


    //$sxml = simplexml_load_string($result);
    //$result = self::convert_simplexml_to_array($sxml);

    $this->session_key = $result;
    //echo "SESSION: " . $this->session_key . " END";

    return $result;
  }

  /**
   * Returns the requested info fields for the requested set of users
   * @param array $uids an array of user ids
   * @param array $fields an array of strings describing the info fields desired
   * @return array of users
   */
  public function people_getUserInfo($guid, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'people/' . $guid . '/@self';
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  public function people_getFriendsInfo($guid, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'people/' . $guid . '/@friends';
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  public function people_getAllInfo($guid, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'people/' . $guid . '/@all';
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  public function people_getGroupFriends($guid, $group_id, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'people/' . $guid . '/' . $group_id;
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  public function people_getMyInfo($fields = Array()) {
    $rest_endpoint = $this->server_addr . '@me/' . '@self';
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  public function people_getFriendInfo($guid, $fid, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'people/' . $guid . '/@all/' . $fid;
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  // get groups associated with a user
  public function group_getUserGroups($guid, $group_id, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'group/' . $guid . '/' . $group_id;
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  // get activities generated by a user
  public function activity_getUserActivity($guid, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'activity/' . $guid . '@self';
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  // get activities generated by a user
  public function activity_getFriendActivity($guid, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'activity/' . $guid . '@friends';
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  // get app data of a user guid for app given by appid
  public function appdata_getUserAppData($guid, $appid, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'appdata/@me/@self/@app'; // . $appid;
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  // get app data of friends of a user guid for app given by appid
  public function appdata_getFriendsAppData($guid, $appid, $fields = Array()) {
    $rest_endpoint = $this->server_addr . 'appdata/' . $guid . '@friends/' . $appid;
    return $this->rest_fetch($rest_endpoint, $fields);
  }

  /* utility */

  public function rest_fetch($endpoints, $params) {

    global $error_codes;

    $httplib = new OpenSocialHttpLib($this->server_addr, $this->oauth_consumer_key, $this->oauth_consumer_secret);
    $xml = $httplib->send_request($endpoints, $params);

    //$result = json_decode($xml, true);
    $result = Zend_Json::decode($xml );

    if ($GLOBALS['opensocial_config']['debug']) {
      // output the raw xml and its corresponding php object, for debugging:
      print '<div style="margin: 10px 30px; padding: 5px; border: 2px solid black; background: gray; color: white; font-size: 12px; font-weight: bold;">';
      $this->cursor++;
      print $this->cursor . ': Called ' . $endpoints . ', show ' .
            '<a href=# onclick="return toggleDisplay(' . $this->cursor . ', \'params\');">Params</a> | '.
            '<a href=# onclick="return toggleDisplay(' . $this->cursor . ', \'xml\');">XML</a> | '.
            '<a href=# onclick="return toggleDisplay(' . $this->cursor . ', \'sxml\');">SXML</a> | '.
            '<a href=# onclick="return toggleDisplay(' . $this->cursor . ', \'php\');">PHP</a>';
      print '<pre id="params'.$this->cursor.'" style="display: none; overflow: auto;">'.print_r($params, true).'</pre>';
      print '<pre id="xml'.$this->cursor.'" style="display: none; overflow: auto;">'.htmlspecialchars($xml).'</pre>';
      print '<pre id="php'.$this->cursor.'" style="display: none; overflow: auto;">'.print_r($result, true).'</pre>';
      print '<pre id="sxml'.$this->cursor.'" style="display: none; overflow: auto;">'.print_r($sxml, true).'</pre>';
      print '</div>';
    }
   
    //var_dump( $result );

    return $result;
  }

  public function rpcGetMyInfo() {
    $rpc_endpoint = "http://sandbox.orkut.com/social/rpc";
    return $this->rpc_fetch($rpc_endpoint, "");
  }

  public function rpc_fetch($rpc_endpoint, $json_body) {

    $httplib = new OpenSocialHttpLib($this->server_addr, $this->oauth_consumer_key, $this->oauth_consumer_secret);
    $json_array['method'] = 'people.get';
    $json_array['id'] = 'myself';
    $json_array['params']['userid'] = '@me';
    $json_array['params']['groupid'] = '@self';

    //$json_body = json_encode($json_array);
    $json_body = Zend_Json::encode($json_array );
    //var_dump($json_array);
    
    $result = $httplib->send_rpc_request($rpc_endpoint, $json_body);

    var_dump( $result );

    return $result;
  }

  public static function convert_simplexml_to_array($sxml) {
    $arr = array();
    if ($sxml) {
      foreach ($sxml as $k => $v) {
        if ($sxml['list']) {
          $arr[] = self::convert_simplexml_to_array($v);
        } else {
          $arr[$k] = self::convert_simplexml_to_array($v);
        }
      }
    }
    if (sizeof($arr) > 0) {
      return $arr;
    } else {
      return (string)$sxml;
    }
  }
}

class OpenSocialClientException extends Exception {
}

/**
 * Error codes and descriptions for the OpenSocial API.
 */

class OpenSocialAPIErrorCodes {

  const OS_ERROR_SUCCESS = 0;

  /*
   * GENERAL ERRORS
   */
  const OS_ERROR_UNKNOWN = 1;
  const OS_ERROR_SERVICE = 2;
  const OS_ERROR_METHOD = 3;
}

function flatten($item, $key, $flat_array) {
     $flat_array[$key] = $item;
}

$filter_array = array(
    "familyName" => 0,
    "givenName" => 0,
    "thumbnailUrl" => 0
);

$error_codes = array(
    "Error 401",
    "Error 501"
);

?>
