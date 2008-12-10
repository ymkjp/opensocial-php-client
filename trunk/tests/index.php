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


// the facebook client library
require_once "../client/OpenSocial.php";

// this defines some of your basic setup
require_once "config.php";

//echo 'Getting my profile info using RPC<br>';
$opensocial->os_client->rpcGetMyInfo();

//echo 'Getting user profile info for any guid<br>';
$opensocial->os_client->people_getUserInfo('04996716008119675151');
//exit;

//echo 'Getting user friends info for any guid<br>';
$opensocial->os_client->people_getFriendsInfo('04996716008119675151');

//echo 'Getting user friends info for any guid<br>';
//$opensocial->os_client->people_getFriendsInfo('05285056827190379419'); //user who has not installed the app; no access allowed 401

//echo 'Getting user friends for any guid info with startIndex=10<br>';
//$opensocial->os_client->people_getFriendsInfo('04996716008119675151', Array('startIndex' => 10) );

//echo 'Getting all users connected to any guid user<br>';
//$opensocial->os_client->people_getAllInfo('04996716008119675151');

// error 501 not implemented
//echo 'Getting requestor profile info<br>';
$opensocial->os_client->people_getMyInfo('04996716008119675151');

// error 401
//echo 'Getting individual pid info known to user guid<br>';
$opensocial->os_client->people_getFriendInfo('04996716008119675151', '05285056827190379419');

//echo 'Getting groups associated to user guid<br>';
$opensocial->os_client->group_getUserGroups('04996716008119675151');

//echo 'Getting activity associated to user guid<br>';
$opensocial->os_client->activity_getUserActivity('04996716008119675151');
//$opensocial->os_client->activity_getFriendActivity('04996716008119675151');

//echo 'Getting appdata associated to user guid for appid<br>';
$opensocial->os_client->appdata_getUserAppData('04996716008119675151',845795770537);
//$opensocial->os_client->activity_getFriendActivity('04996716008119675151');



?>
