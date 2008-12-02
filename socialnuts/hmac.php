<?php
  require_once("../client/OAuth.php");
 
  if (verifySignature()) {
    print ('Validated HMAC!');
  } else {
    print ('Spoofed HMAC');
  }

  function verifySignature() {
    //Build a request object from the current request
    $request = OAuthRequest::from_request(null, null, array_merge($_GET, $_POST));

    // Initialize consumer info. including consumer key and secret
    $consumer = new OAuthConsumer('orkut.com:623061448914', 'uynAeXiWTisflWX99KU1D2q5', null);

    //Initialize the new signature method
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
 
    //Check the request signature
    @$signature_valid = $signature_method->check_signature($request, $consumer, null, $_GET["oauth_signature"]);

    return $signature_valid;
  }
?>
