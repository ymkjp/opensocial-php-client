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


// the opensocial client library
include_once '../client/OpenSocial.php';

// the opensocial client library
include_once 'session.php';

// some basic library functions
include_once 'lib.php';

// this defines some of your basic setup
include_once 'config.php';

// this defines a basic OpenSocialUser class that can be extended 
include_once 'osuser.php';

if( isset($_GET['tab']) ) {
    $tab = $_GET['tab'];
}
else {
    $tab = '';
}

if( isset($_GET['st']) && isset($_GET['u']) ) {
    //$token = 'st=' . $_GET['st'];
    $user = $_GET['u'];
}

// Initialize session: server side file based
// Could be modified to be DB-based 
init_session($user);


// get info of the viewer
if( !isset($_SESSION['userInfo']) ) {
    $userInfo = $opensocial->os_client->people_getUserInfo($user);
    $_SESSION['userInfo'] = $userInfo;
}
else {
    $userInfo = $_SESSION['userInfo'];
}


$viewerThumbnail = $userInfo['entry']['thumbnailUrl'];
$viewerFirstName = $userInfo['entry']['name']['givenName'];
$viewerLastName = $userInfo['entry']['name']['familyName'];

$gFriends[$user]['givenName'] = $viewerFirstName;
$gFriends[$user]['familyName'] = $viewerLastName;

$viewer = new OpenSocialUser($user);
$viewer->setNames($viewerFirstName, $viewerLastName);
$viewer->setThumbnail($viewerThumbnail);

if( !isset($_SESSION['userFriends']) ) {
    $userFriends = $opensocial->os_client->people_getFriendsInfo($user);
    $_SESSION['userFriends'] = $userFriends;
}
else {
    $userFriends = $_SESSION['userFriends'];
}

if (isset($_GET['to'])) {
  $received = $_GET['to'];
}

foreach( $userFriends['entry'] as $i => $f ) {
  $gFriends[$f['id']]['givenName'] = $f['name']['givenName'];
  $gFriends[$f['id']]['familyName'] = $f['name']['familyName'];
}

if (isset($_POST['to'])) {
  $nut = $_POST['nut'];
  $to = $_POST['to'];
  $nuts = send_nut($user, $to, $nut);
  $msg = "You have sent a $nut to " . $gFriends[$to]['givenName'];
}
else {
  $msg = "Would you like to send some social nuts to your friends?";
}

$nuts = get_nuts($user, 'sent');

if( $tab == '' ) {
?>
<div style="padding: 10px;">
  <div style="float:left; width:100%;">
  
  <div style="float:left; margin:10px; padding:10px; font-size:120%; width:60%; background:#FFFFCC;">
  <h2>Hi <?php echo $viewer->getFirstName() ?>!<h2>
  <p style="font-weight:normal; font-size:70%;"><?php echo $msg; ?></p>
  </div>

  <div style="float:right; margin: 20px; width:20%;">
  <a href="?u=<?php echo $user ?>"><img src="<?php echo $viewer->getThumbnail() ?>"></a><br>
  <div><?php echo $viewer->getFirstName() ?> <?php echo $viewer->getLastName() ?></div>
  </div>

  </div>

    <form method="post" >
    <div style="float:left; width:100%;">
    <div style="float:left; font-weight:bold; font-size:120%; margin:10px; padding:10px;">Step 1. Pick a nut</div>
    <div style="float:left; font-weight:bold; font-size:120%; margin:10px; padding:10px;">
    <select style="width:150" name="nut" 
      <option value ="Hazelnut" selected="selected">Hazelnut</option>
      <option value ="Walnut">Walnut</option>
      <option value ="Pistachio">Pistachio</option>
      <option value ="Cashew">Cashew</option>
      <option value ="Peanut">Peanut</option>
    </select>
    </div>
    </div>
    <div style="float:left; width:100%;">
    <div style="float:left; width: 30%; font-weight:bold; font-size:120%; margin:10px; padding:10px;">Step 2. Choose friends</div>
    <div style="float:left; width: 50%; font-weight:bold; font-size:120%; margin:10px; padding:10px;">Step 3. 
      <input value="Send a nut" type="submit"/>
    </div>
    </div>
    
    <div style="float:left; margin:5px 1% 5px 1%; padding:1%; width:95%; background:#ccccff; border: 1px solid #3B5898;">
<?php
    $cnt = 0;
    foreach( $userFriends['entry'] as $i => $f ) {
        echo '<div style="float:left; width: 100px; height: 110px; font-weight:normal; font-size:80%; margin:5px; padding:5px;">';
        echo '<a href="?tab=received&to=' . $f['id'] . '&u=' . $user . '">';
        echo '<img src="' . $f['thumbnailUrl'] . '"></a><br>';
        echo '<input type="radio" name="to" value="' . htmlentities($f['id']) . '"><br>';
        echo htmlentities($f['name']['givenName']) . " " . htmlentities($f['name']['familyName']) . '<br>';
        echo '</div>';
        $gFriends[$f['id']]['givenName'] = $f['name']['givenName'];
        $gFriends[$f['id']]['familyName'] = $f['name']['familyName'];
        $cnt++;
    }
    echo '</div>';

?>
    </div>
    </form>
</div>


<?php


}

if( $tab == 'sent' ) {
    echo '<div style="float:left; margin:5px 2% 5px 1%; padding:1%; width:80%; background:#EEEEFF; border: 1px solid #3B5898; font-weight:bold; font-size:150%;">';
    echo 'You have sent friends the following nuts';
    echo '</div>';
    show_sent($nuts, 100);
}

if( $tab == 'received' ) {
    $nuts = get_nuts($user, 'sent');

    echo '<div style="float:left; margin:5px 1% 5px 1%; padding:1%; width:50%; background:#ccccff; border: 1px solid #3B5898;">';
    foreach( $userFriends['entry'] as $i => $f ) {
        echo '<div style="float:left; width: 100px; height: 110px; font-weight:normal; font-size:80%; margin:5px; padding:5px;">';
        echo '<input type="radio" name="to" value="' . htmlentities($f['id']) . '"><br><br>';
        echo '<a href="?tab=received&to=' . $f['id'] . '&u=' . $user . '">';
        echo '<img src="' . $f['thumbnailUrl'] . '"></a><br>';
        echo htmlentities($f['name']['givenName']) . " " . htmlentities($f['name']['familyName']) . '<br>';
        echo '</div>';
        $gFriends[$f['id']]['givenName'] = $f['name']['givenName'];
        $gFriends[$f['id']]['familyName'] = $f['name']['familyName'];
        $cnt++;
    }
    echo '</div>';

    if( isset($received) ) {
        $nuts = get_nuts($received, 'received');
        if( !empty($nuts) ) {
            show_received($nuts, 100);
        }
        else {
            echo '<div style="float:right; margin:5px 2% 5px 1%; padding:1%; width:50%; background:#EEEEFF; border: 1px solid #3B5898; font-weight:normal; font-size:150%;">';
            echo htmlentities($gFriends[$received]['givenName']) . ' has not received any nuts.';
            echo '</div>';
        }
    }
    else {
        echo '<div style="float:left; margin:5px 2% 5px 1%; padding:1%; width:40%; background:#EEEEFF; border: 1px solid #3B5898; font-weight:bold; font-size:150%;">';
        echo 'Click on friends to see what they have received';
        echo '</div>';
    }
}

function show_sent($nuts, $max) {
  global $gFriends;

  $i = 0;
  echo '<div style="float:left; margin:5px 1% 5px 1%; padding:1%; width:45%; background:#EEEEFF; border: 1px solid #3B5898;">';
  foreach ($nuts as $i => $s) {
     $from = $s['from'];
     $to = $s['to'];
     $nut = $s['nut'];
     echo '<span style="margin: 0px 10px 0px 10px; font-weight:bold; font-size:100%; color: #3B5898;">' . htmlentities($gFriends[$from]['givenName']) . "</span> sent " . '<span style="margin: 0px 10px 0px 10px; font-weight:bold; font-size:100%; color: #3B5898;">' . htmlentities($gFriends[$to]['givenName']) . '</span> a <span style="margin: 0px 10px 0px 10px; font-weight:bold; font-size:110%; color: #FF0000;">' . $nut . '</span><br>';
  }
  echo '</div>';
}

function show_received($nuts, $max) {
  global $gFriends;

  $i = 0;
  echo '<div style="float:right; margin:5px 2% 5px 1%; padding:1%; width:40%; background:#EEEEFF; border: 1px solid #3B5898;">';
  foreach ($nuts as $i => $s) {
     $from = $s['from'];
     $to = $s['to'];
     $nut = $s['nut'];
     echo '<span style="margin: 0px 10px 0px 10px; font-weight:bold; font-size:100%; color: #3B5898;">' . htmlentities($gFriends[$to]['givenName']) . "</span> received from " . '<span style="margin: 0px 10px 0px 10px; font-weight:bold; font-size:100%; color: #3B5898;">' . htmlentities($gFriends[$from]['givenName']) . '</span> a <span style="margin: 0px 10px 0px 10px; font-weight:bold; font-size:110%; color: #FF0000;">' . $nut . '</span><br>';
  }
  echo '</div>';
}


?>
