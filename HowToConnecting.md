To use the client library, you must first configure it to connect to an OpenSocial-compatible container.  To do this, you need to create two objects:

  * An `osapiAuth` instance, which determines how the client library will identify your application to the container.
  * An `osapiProvider` instance, which contains the configuration for a specific container.

Additionally, you may need two additional objects, depending on which containers you are connecting to and how you are connecting to them:

  * An `osapiStorage` instance, which provides persistent data storage on your server.
  * A `localUserId` value, which represents a unique local ID number for users of your website.

With these objects, you will be able to create an instance of an `osapi` class, which can be used for making OpenSocial calls.





# Creating an `osapiStorage` instance #

For XRDS and 3-legged operations, you will need to create an object that can store persistent data across page loads.  The easiest way to do that is to use a temporary file in your filesystem:

```
$storage = new osapiFileStorage('/tmp/osapi');
```

Where `/tmp/osapi` is the path to a temporary directory that can be written by PHP.  The directory specified here will be created if it does not exist.

# Obtaining a `localUserId` value #

For 3-legged OAuth you will need to link a local user identifier with a remote user ID.  If you already have a unique ID for the current user, you can use this.  Otherwise, just use a session value:

```
session_start();
$localUserId = session_id();
```

# Creating an `osapiProvider` instance #
The `osapiProvider` class manages a connection to an OpenSocial compatible container.  Many OpenSocial containers come pre-configured in the library, but it is also possible to obtain an instance of an `osapiProvider` by using XRDS or manually configuring the URLs for a provider.  It is recommended that you use the pre-configured providers where possible, though, as they container container-specific code to produce a more consistent developer experience across containers.

## Using a pre-configured provider ##

Several containers have come pre-configured for use in the client library.  Following is a listing showing how to create instances of the providers for each pre-configured container:

| **iGoogle, Gmail** | `$provider = new osapiGoogleProvider();` |
|:-------------------|:-----------------------------------------|
| **Google Friend Connect** | `$provider = new osapiFriendConnectProvider();` |
| **Hi5**            | `$provider = new osapiNetlogProvider();` |
| **MySpace**        | `$provider = new osapiMySpaceProvider();` |
| **Netlog**         | `$provider = new osapiNetlogProvider();` |
| **orkut**          | `$provider = new osapiOrkutProvider();`  |
| **Partuza**        | `$provider = new osapiPartuzaProvider();` |
| **Plaxo**          | `$provider = new osapiPlaxoProvider();`  |


## Using XRDS ##

XRDS is a way for a container to declare configuration information.  To obtain this configuration, just create an `osapiXrdsProvider` instance with the URL of the website hosting the XRDS file.  Note that you will need to have configured a `osapiStorage` instance to pass to the constructor as well.

```
$provider = new osapiXrdsProvider('http://en.netlog.com/', $storage);
```

## Manually configuring a provider ##

If you are writing code for a container which is not pre-configured in the client library, then just create an instance of the `osapiProvider` class directly:

```
$provider = new osapiProvider($requestTokenUrl, $authorizeUrl, $accessTokenUrl, $restEndpoint, $rpcEndpoint, $providerName, $isOpenSocial, $httpProvider);
```

The parameters needed are:

|requestTokenUrl| `String` | The OAuth URL used to obtain an unauthorized Request Token. |
|:--------------|:---------|:------------------------------------------------------------|
|authorizeUrl   | `String` | The OAuth URL used to obtain User authorization for Consumer access. |
|accessTokenUrl | `String` | The OAuth URL used to exchange the User-authorized Request Token for an Access Token. |
|restEndpoint   | `String` | The base URL of the container's OpenSocial REST API implementation.|
|rpcEndpoint    | `String` | The base URL of the container's OpenSocial RPC API implementation.|
|providerName   | `String` | A name identifying this provider.                           |
|isOpenSocial   | `Boolean` | True if this container implements OpenSocial, false if they are only a Portable Contacts provider. |
|httpProvider   | `osapiHttpProvider` | _Optional_.  A class to control how the client library makes HTTP requests.  Will default to CURL if omitted. |


# Creating an `osapiAuth` instance #

## 2-legged OAuth ##
2-legged OAuth authorizes your app to access the information of anyone who has installed your application on the container you are connecting to.  This style of authorization is best for background processing for an app which runs inside of a social network.

To create a 2-legged `osapiAuth` object, first obtain the **consumer key** and **consumer secret** values for your application.  You will usually be able to obtain these values when registering your application with the container.  For Google's OpenSocial containers (_orkut_, _iGoogle_, _Gmail_) you will need to fill out this form to obtain a 2-legged key for your application: https://www.google.com/gadgets/directory/verify

Once you have these values, use them to create an `osapiOAuth2Legged` object:
```
$auth = new osapiOAuth2Legged("<consumer key>", "<consumer secret>");
```

If you need to make 2-legged requests on behalf of a user (for example, updating AppData for a specific user), you will need to pass that user's OpenSocial ID to the `osapiOAuth2Legged` constructor.

```
$auth = new osapiOAuth2Legged("<consumer key>", "<consumer secret>, <user ID>");
```

## 3-legged OAuth ##
3-legged OAuth allows users who have not installed your application on a social network to grant access to their data to your application.  This style of authorization is best for websites which want to work with social data but do not run applications inside of social networks.

To create a 3-legged `osapiAuth` object, first obtain the **consumer key** and **consumer secret** values for your application as described in the 2-legged OAuth section.  You will also need an instance of an `osapiStorage` class, an instance of an `osapiProvider` class, and a `localUserID`, which you pass to the `osapiOAuth3Legged::performOAuthLogin` call:

```
$auth = osapiOAuth3Legged::performOAuthLogin("<consumer key">, "<consumer secret>", $storage, $provider, $localUserId);
```

Note that when you run the `osapiOAuth3Legged::performOAuthLogin` method, the entire page will be redirected to the appropriate container's 3-legged authorization page.  If you wish to delay the redirect, you will need to restructure your code appropriately.

## Security Token ##
If you are making REST calls for a gadget running inside of a social network, you can use the gadget's security token to authorize your requests.  Once you pass the value of the security token from your gadget to the server backend, use:

```
$auth = new osapiSecurityToken("<security token>");
```

## FCAuth Token ##
Google Friend Connect supports using [a site authentication cookie](http://code.google.com/apis/friendconnect/opensocial_rest_rpc.html#site-cookie) to authorize your client library requests.  This works in the same way as the security token, but needs to be passed to the RPC or REST endpoint as a parameter named `fcauth`.  Configure your client in the following manner to use this parameter:

```
$auth = new osapiFCAuth("<fcauth token>");
```

The FCAuth token value is sent as a cookie to your server when a user is logged in.  You should be able to access the value of this token by checking for a cookie named `fcauth<site ID number>`:

```
$site_id = "<your friend connect site ID goes here>";
$fcauth_token = $_COOKIE["fcauth" . $site_id];
```


# Creating an `osapi` instance #
When you have obtained an auth and provider instance, pass them to the `osapi` constructor to get an object you can use to make social requests:

```
$osapi = new osapi($provider, $auth);
```

# Examples #
Following are some examples for configuring the library.

## 2-legged OAuth on orkut ##
```
$provider = new osapiOrkutProvider();
$auth = new osapiOAuth2Legged("<consumer key>", "<consumer secret>", "<OpenSocial user ID>");
$osapi = new osapi($provider, $auth);
```

## 3-legged OAuth on hi5 ##
```
session_start();
$localUserId = session_id();
$storage = new osapiFileStorage('/tmp/osapi');
$provider = new osapiHi5Provider();
$auth = osapiOAuth3Legged::performOAuthLogin("<consumer key>", "<consumer secret>", $storage, $provider, $localUserId);
$osapi = new osapi($provider, $auth);
```