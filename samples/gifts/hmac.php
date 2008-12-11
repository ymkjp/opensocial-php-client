<?php
  require_once("../client/OAuth.php");
  require_once ('config.php');
 
  if (verifySignature()) {
    //print ('Validated HMAC!');
  } else {
    print ('Spoofed HMAC');
	die;
  }

  function verifySignature() {
	global $oauth_consumer_secret;
    //Build a request object from the current request
    $request = OAuthRequest::from_request(null, null, array_merge($_GET, $_POST));

    // Initialize consumer info. including consumer key and secret
    $consumer = new OAuthConsumer($_GET['oauth_consumer_key'], $oauth_consumer_secret, null);
    //Initialize the new signature method
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
 
    //Check the request signature
    @$signature_valid = $signature_method->check_signature($request, $consumer, null, $_GET["oauth_signature"]);

    return $signature_valid;
  }
?>
