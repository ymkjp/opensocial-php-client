<?php
require_once 'PHPUnit/Framework.php';
require_once '../client/OpenSocial.php';
require_once("../client/OAuth.php");
 
// Temporary user id for orkut
// Until 2-legged oauth is ready to authenticate
$user = '04996716008119675151';

class MyOSClientTest extends PHPUnit_Framework_TestCase
{
    protected $_opensocial = null;

    public function setUp()
    {
        //$oauth_consumer_key = 'orkut.com:YOUR_CONSUMER_KEY_HERE';
        //$oauth_consumer_secret = 'YOUR_CONSUMER_SECRET_HERE';
        $oauth_consumer_key = 'orkut.com:623061448914';
        $oauth_consumer_secret = 'uynAeXiWTisflWX99KU1D2q5';

        // Create instance
        $this->_opensocial = new OpenSocial($oauth_consumer_key, $oauth_consumer_secret);

    }

    public function tearDown()
    {
        unset($this->_opensocial);
    }

    public function testAPIKey()
    {
	$this->assertEquals('orkut.com:623061448914', $this->_opensocial->oauth_consumer_key);
    }

    public function testAPISecret()
    {
	$this->assertEquals('uynAeXiWTisflWX99KU1D2q5', $this->_opensocial->oauth_consumer_secret);
    }

    public function testAPIUser()
    {
	$this->_opensocial->set_user(12345, '', null);
	$this->assertEquals(12345, $this->_opensocial->get_current_user());
    }

    public function testContainerURL()
    {
	$this->assertEquals("http://www.orkut.com/social/rest/", $this->_opensocial->get_container_url());
	$this->assertEquals("http://sandbox.orkut.com/social/rest/", $this->_opensocial->get_container_url("sandbox"));
    }

    public function testGetMyInfoRPC()
    {
        $expected = '{"id":"myself","data":{"photos":[{"value":"http://img2.orkut.com/images/small/1207177615/161157888.jpg","type":"thumbnail"}],"id":"04996716008119675151","isViewer":true,"thumbnailUrl":"http://img2.orkut.com/images/small/1207177615/161157888.jpg","name":{"familyName":"shen","givenName":"shawn"},"isOwner":false}}';
        //echo 'Getting my profile info using RPC<br>';
	$result = $this->_opensocial->os_client->rpcGetMyInfo();
	$this->assertEquals($expected, $result);
    }

    public function testGetUserInfo()
    {
	$result = $this->_opensocial->os_client->people_getUserInfo('04996716008119675151');
        $r_id = $result["entry"]["id"];
	$this->assertEquals("04996716008119675151", $r_id); 
    }

    public function testGetFriendsInfo()
    {
	$result = $this->_opensocial->os_client->people_getFriendsInfo('04996716008119675151');
        $r_type = $result["entry"][0]["photos"][0]["type"];
	$this->assertEquals("thumbnail", $r_type); 
    }

    public function testGetGroup()
    {
	$result = $this->_opensocial->os_client->group_getUserGroups('04996716008119675151'); 
        $expected = '<HTML>
<HEAD>
<TITLE>The service group is not implemented</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000">
<H1>The service group is not implemented</H1>
<H2>Error 501</H2>
</BODY>
</HTML>
';
	$this->assertEquals($expected, $result); 
    }

    public function testGetActivity()
    {
        $expected = '<HTML>
<HEAD>
<TITLE>The service activity is not implemented</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000">
<H1>The service activity is not implemented</H1>
<H2>Error 501</H2>
</BODY>
</HTML>
';

	$result = $this->_opensocial->os_client->activity_getUserActivity('04996716008119675151');
	$this->assertEquals($expected, $result); 
    }

    public function testGetAppData()
    {
        $expected = '<HTML>
<HEAD>
<TITLE>Only app data for the current application can be accessed or modified</TITLE>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TEXT="#000000">
<H1>Only app data for the current application can be accessed or modified</H1>
<H2>Error 403</H2>
</BODY>
</HTML>
';

	$result = $this->_opensocial->os_client->appdata_getFriendsAppData('04996716008119675151','845795770537');
	$this->assertEquals($expected, $result); 
    }

}
