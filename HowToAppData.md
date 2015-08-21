This page will demonstrate how to work with App Data stored on an OpenSocial container.



# Fetching #

## Creating the request ##

The request for obtaining app data is:

```
$request = $osapi->appdata->get($params);
```

The `$params` parameter is an associative array of values you can use to customize the request.

| **Key** | **Type** | **Description** |
|:--------|:---------|:----------------|
| `userId` | `string` | ID of the user to operate on.  You may set this to **"@me"** to fetch data for the current user |
| `groupId` | `string` | The name of the group of people to fetch App Data for.  You may set this to **"@self"** to only fetch data for the user corresponding to `userId`, or **"@friends"** to fetch data for the user's friends. |
| `appId` | `string` | The ID of the application to fetch App Data for. You may use **"@app"** to fetch data for the current application. |
| `fields` | `array`  | An array of App Data keys to fetch. |

Additional parameters you can use are listed in the [OpenSocial specification](http://www.opensocial.org/Technical-Resources/opensocial-spec-v09/REST-API.html#standardQueryParameters).

## Response ##

The response for a successful request is an associative array where the keys are IDs corresponding to OpenSocial user IDs, and the values are associative arrays of App Data key/value pairs:

```
Array
(
    [<user id>] => Array
        (
            [<key 1>] => <value 1>
            [<key 2>] => <value 2>
        )

)
```

# Creating / Updating #

## Creating the requests ##

The request for creating app data is:

```
$request = $osapi->appdata->create($params);
```

The request for updating app data is:

```
$request = $osapi->appdata->update($params);
```

The `$params` parameter is an associative array of values you can use to customize either of these requests.

| **Key** | **Type** | **Description** |
|:--------|:---------|:----------------|
| `userId` | `string` | ID of the user to operate on.  You may set this to **"@me"** to set data for the current user |
| `groupId` | `string` |  You may set this to **"@self"** to update data for the user corresponding to `userId`.  Most containers will not support setting App Data for anything other than @self. |
| `appId` | `string` | The ID of the application to set App Data for. You may use **"@app"** to set data for the current application. |
| `data`  | `array`  | An array of App Data key/value pairs to set.  |

For example, to set values for `key1`, `key2`, and `key3` for the current user, you would set `$params` to:

```
  $params = array(
      'userId' => '@me', 
      'groupId' => '@self', 
      'appId' => '@app',
      'data' => array(
          'key1' => 'value 1', 
          'key2' => 'value 2', 
          'key3' => 'value 3'
      )
  );
```

## Response ##

Creating or updating App Data has succeeded if the response is not an instance of `osapiError`.