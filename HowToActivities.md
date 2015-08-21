This page will demonstrate how to work with activities posted to an OpenSocial container.



# Fetching #

## Creating the request ##

The request for obtaining app data is:

```
$request = $osapi->activities->get($params);
```

The `$params` parameter is an associative array of values you can use to customize the request.

| **Key** | **Type** | **Description** |
|:--------|:---------|:----------------|
| `userId` | `string` | ID of the user to operate on.  You may set this to **"@me"** to fetch activities for the current user |
| `groupId` | `string` | The name of the group of people to fetch activities for.  You may set this to **"@self"** to only fetch activities for the user corresponding to `userId`, or **"@friends"** to fetch activities for the user's friends. |
| `count` | `number` | Maximum number of results to return. |

Additional parameters you can use are listed in the [OpenSocial specification](http://www.opensocial.org/Technical-Resources/opensocial-spec-v09/REST-API.html#standardQueryParameters).

## Response ##

[osapiCollection](http://code.google.com/p/opensocial-php-client/source/browse/trunk/src/osapi/model/osapiCollection.php) of [osapiActivity](http://code.google.com/p/opensocial-php-client/source/browse/trunk/src/osapi/model/osapiActivity.php) objects.

```
osapiCollection Object
(
    [list] => Array
        (
            [0] => osapiActivity Object
                (
                    [body] => Activity body 1
                    [id] => 6743
                    [postedTime] => 1236727134
                    [streamTitle] => activities
                    [title] => Activity title 1
                    [userId] => 12345
                )

            [1] => osapiActivity Object
                (
                    [body] => Activity body 2
                    [id] => 6741
                    [postedTime] => 1236727131
                    [streamTitle] => activities
                    [title] => Activity title 2
                    [userId] => 12345
                )

          
        )

    [startIndex] => 0
    [totalResults] => 47
    [itemsPerPage] => 2
    [filtered] => 
    [sorted] => 
    [updatedSince] =>
)
```

# Creating #

## Creating the request ##

The request for creating a new activity is:

```
$request = $osapi->activities->create($params);
```


The `$params` parameter is an associative array of values you can use to customize either of these requests.

| **Key** | **Type** | **Description** |
|:--------|:---------|:----------------|
| `userId` | `string` | ID of the user to operate on.  You may set this to **"@me"** to set an activity for the current user |
| `groupId` | `string` |  You may set this to **"@self"** to post an activity for the user corresponding to `userId`.  Most containers will not support creating activities for anything other than @self. |
| `activity` | `osapiActivity` | An `osapiActivity` object representing the activity to post.  |

The simplest way to create an [osapiActivity](http://code.google.com/p/opensocial-php-client/source/browse/trunk/src/osapi/model/osapiActivity.php) object is to create a new object and set the title and body:

```
$activity = new osapiActivity();
$activity->setTitle('Activity title');
$activity->setBody('Activity body');
```

Then, to build the request:

```
$params = array(
  'userId' => '@me',
  'groupId' => '@self',
  'activity' => $activity,
);
$request = $osapi->activities->create($params);
```

## Response ##

Posting an activity has succeeded if the response is not an instance of `osapiError`.