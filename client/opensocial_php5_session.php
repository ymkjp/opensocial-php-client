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


function init_session($uid)
{
  global $sess_save_path;

  if( isset($_SESSION) ) {
      session_destroy();
  }

  session_id($uid);

  session_set_save_handler("open", "close", "read", "write", "destroy", "gc");
  session_start();
}

function open($save_path, $session_name)
{
  global $sess_save_path;

  return(true);
}

function close()
{
  return(true);
}

function read($id)
{
  global $sess_save_path;

  $sess_file = "$sess_save_path/sess_$id";
  return (string) @file_get_contents($sess_file);
}

function write($id, $sess_data)
{
  global $sess_save_path;

  $sess_file = "$sess_save_path/sess_$id";
  if ($fp = @fopen($sess_file, "w")) {
    $return = fwrite($fp, $sess_data);
    fclose($fp);
    return $return;
  } else {
    return(false);
  }

}

function destroy($id)
{
  global $sess_save_path;

  $sess_file = "$sess_save_path/sess_$id";
  return(@unlink($sess_file));
}

function gc($maxlifetime)
{
  global $sess_save_path;

  foreach (glob("$sess_save_path/sess_*") as $filename) {
    if (filemtime($filename) + $maxlifetime < time()) {
      @unlink($filename);
    }
  }
  return true;
}

// proceed to use sessions normally

?>
