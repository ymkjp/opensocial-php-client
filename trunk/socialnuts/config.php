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

// 
require_once("../client/OAuth.php");

$oauth_consumer_key = 'orkut.com:623061448914';
$oauth_consumer_secret = 'uynAeXiWTisflWX99KU1D2q5';

// Temporary user id for orkut
// Until 2-legged oauth is ready to authenticate
$user = '04996716008119675151';

if( isset($_GET['debug']) && $_GET['debug'] == 1 ) {
    $opensocial_config['debug'] = true;
}
else {
    $opensocial_config['debug'] = false;
}

// location of session file e.g. on a filer
$sess_save_path = '/tmp/socialnuts/';

// Create instance 
$opensocial = new OpenSocial($oauth_consumer_key, $oauth_consumer_secret);


// The IP address of your database
$db_ip = 'localhost';           

$db_user = 'nuts123';
$db_pass = 'opensocial';  // change to YOUR_DB_PASSWORD

// the name of the database that you create for footprints.
$db_name = 'socialnuts';

/* create this table on the database:
DROP TABLE IF EXISTS `socialnuts`; 
CREATE TABLE `socialnuts` (
  `from` varchar(50) NOT NULL default '0',
  `to` varchar(50) NOT NULL default '0',
  `nut` varchar(20) NOT NULL default '0',
  `ts` int(11) NOT NULL default '0',
  KEY `from` (`from`),
  KEY `to` (`to`)
)
*/
