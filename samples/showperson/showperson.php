<h1>Show Person Example</h1>
<p>This sample shows how to fetch information about one user.</p>

<?php
// Add the library directory to the inclue path
set_include_path(get_include_path() . PATH_SEPARATOR . 
    '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library');

// Include the client library
require_once('OpenSocial/OpenSocial.php');


$opensocial = new OpenSocial('orkut.com:623061448914', 'uynAeXiWTisflWX99KU1D2q5');
$user = '04996716008119675151';

$person = $opensocial->os_client->people_getUserInfo('04996716008119675151');
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
</table>
