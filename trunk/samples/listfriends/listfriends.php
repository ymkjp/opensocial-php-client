<h1>List Friends Example</h1>
<p>This sample shows how to fetch a user's friends and display them.</p>

<?php
// Add the library directory to the inclue path
set_include_path(get_include_path() . PATH_SEPARATOR . 
    '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library');

// Include the client library
require_once('OpenSocial/OpenSocial.php');


$opensocial = new OpenSocial('orkut.com:623061448914', 'uynAeXiWTisflWX99KU1D2q5');
$user = '04996716008119675151';

$result = $opensocial->os_client->people_getFriendsInfo('04996716008119675151');

// Print the paging information
echo sprintf("Showing friends %s to %s of %s", $result->startIndex + 1, 
    count($result), $result->totalResults);

// Iterate over the friends and print the names of each
echo "<ol>";
foreach ($result as $person) {
  echo sprintf("<li>%s</li>", htmlentities($person->getDisplayName()));
}
echo "</ol>";
