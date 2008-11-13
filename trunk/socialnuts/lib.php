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

function get_db_conn() {
  $conn = mysql_connect($GLOBALS['db_ip'], $GLOBALS['db_user'], $GLOBALS['db_pass']);
  mysql_select_db($GLOBALS['db_name'], $conn);
  return $conn;
}

function get_nuts($user, $flag='sent') {
  $conn = get_db_conn();

  if( $flag === 'sent' ) {
      $res = mysql_query('SELECT `from`, `to`, `ts`, `nut` FROM socialnuts WHERE `from`="' . $user . '" ORDER BY `ts` DESC', $conn);
  }
  else {
      $res = mysql_query('SELECT `from`, `to`, `ts`, `nut` FROM socialnuts WHERE `to`="' . $user . '" ORDER BY `ts` DESC', $conn);
  }
  $nuts = array(); 

  if( $res ) {
    while ($row = mysql_fetch_assoc($res)) {
      $nuts[] = $row;
    }
  }
  return $nuts;
}

function send_nut($from, $to, $nut) {
  global $opensocial;

  $conn = get_db_conn();
  mysql_query('INSERT INTO socialnuts SET `from`="'.$from.'", `ts`='.time().', `to`="'.$to.'", `nut`="'.$nut.'"', $conn);

  $nuts = get_nuts($from);
  return $nuts;
}

