<!--
 Copyright 2008 Google Inc.

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
-->

<h1>Fetch Profile Fields Example</h1>
<p>This sample shows how to fetch additional profile fields for a user.</p>

<?php
// Add the library directory to the inclue path
set_include_path(get_include_path() . PATH_SEPARATOR . 
    '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library');

// Include the client library
require_once('OpenSocial/OpenSocial.php');

$config = array(
  "oauth_consumer_key" => "orkut.com:623061448914",
  "oauth_consumer_secret" => "uynAeXiWTisflWX99KU1D2q5",
  "server_rest_base" => "http://sandbox.orkut.com/social/rest/"
);
$opensocial = new OpenSocial($config);
$params = array(
  'fields' => array('languagesSpoken', 'thumbnailUrl', 'name')
);
$req = new FetchPersonRequest('03067092798963641994', $params);
$person = $opensocial->request($req);

?>

<table>
  <tr>
    <th>Name:</th>
    <td><?= $person->getDisplayName() ?></td>
  </tr>
  <tr>
    <th>Id:</th>
    <td><?= $person->getId() ?></td>
  </tr>
  <tr>
    <th>Thumbnail:</th>
    <td><img src='<?= $person->getField("thumbnailUrl") ?>' /></td>
  </tr>
  <tr>
    <th>Languages I speak:</th>
    <td><?= implode($person->getField('languagesSpoken'), ",") ?></td>
  </tr>
</table>
