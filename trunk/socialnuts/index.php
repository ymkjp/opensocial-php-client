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
require_once '../client/OpenSocial.php';

// the opensocial client library
require_once '../client/OpenSocialSession.php';

// some basic library functions
require_once 'lib.php';

// this defines some of your basic setup
require_once 'config.php';

// this defines a basic OpenSocialUser class that can be extended 
require_once 'osuser.php';

$thisPage = "THE_PUBLIC_URL_OF_THIS_PAGE";

if(isset($_GET['opensocial_viewer_id'])) {
  //verify the signature first
  
  require_once("hmac.php");

  $user = $_GET['opensocial_viewer_id'];
  init_session($user, true);
  $_SESSION["user"] = $user;
  echo("<iframe width='98%' height='500px' frameborder='0' src='" . $thisPage . "?sessid=" . $user . "' />");
  die;
} else if (isset($_GET['sessid'])) {
  $sessid = $_GET['sessid'];
  init_session($sessid, false);
  $user = $_SESSION['user'];
} else {
  print ("provide either userid or session id");
  die;
}

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

//populate gFriends

foreach( $userFriends['entry'] as $i => $f )  {
  $gFriends[$f['id']] = $f;
}

//now  add the gift given if any

if(isset($_GET['to'])) {
	$nut = $_GET['nut'];
	$to = $_GET['to'];
	$comments = $_GET['comments'];
	send_nut($user, $to, $nut, $comments);
}

//and then display the given gifts

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 TRANSITIONAL//EN">
  <html>
    <head>
      <title>OpenSocial - Rest Sample</title>
	  <link rel="stylesheet" type="text/css" href="../css/default.css">
   </head>
   <body>
		<div id='content'>

<?PHP
$nuts = get_nuts($user, 'sent');

echo("<ul id='recent'>");

for($i = 0; $i < count($nuts); $i ++) {
	$friendNow = $gFriends[$nuts[$i]['to']];
	if($i%2 == 0) {
		echo ("<li class='even'><div class='gift'>");
	} else {
		echo ("<li class='odd'><div class='gift'>");
	}

	echo ("<img src='" . $friendNow['thumbnailUrl'] . "'/>");
	echo ("You sent " . $friendNow['name']['givenName'] . " " . $friendNow['name']['familyName'] . 
		  " a " . $nuts[$i]["nut"] . " @ " . date("F j, Y, g:i a e", $nuts[$i]["ts"]));
	echo ("<div class='note'><p class='note'>" . $nuts[$i]['comments'] . "</p></div>");
	echo ("</div></li>");
}


echo("</ul>");

echo("<form method='GET' action='" . $thisPage . "'>");

$friendPicker = "<select name='to'>";

foreach($gFriends as $id => $f)  {
  $friendPicker .= "<option value='" . $id . "'>" . htmlentities($f['name']['givenName']) . 
	               " " . htmlentities($f['name']['familyName']) . "</option><br/>";
}

$friendPicker .= "</select>";

$itemPicker =  "<select name='nut'>" . 
					"<option value ='spaghetti' selected='selected'>spaghetti</option>" .
					"<option value ='cake'>cake</option>" .
					"<option value ='pear'>pear</option>" .
					"<option value ='PHP (it`s recursive!)'>PHP (it`s recursive!)</option>" .
				"</select>";

echo("<p>Give " . $friendPicker . " a " . $itemPicker . "</p>");
echo("Note : <input type='text' name='comments'/>");
echo("<input type='hidden' name='sessid' value='" . $_GET['sessid'] . "'/>");
echo("<input type='submit' value='Send'/>");

echo("</form");

echo("</div>");
?>

   </body>
</html>
