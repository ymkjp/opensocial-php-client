This page will demonstrate how to work with Person data from an OpenSocial container.



# Fetching #

## Creating the request ##

The request for obtaining a set of people is:

```
$request = $osapi->people->get($params);
```

The `$params` parameter is an associative array of values you can use to customize the request.

| **Key** | **Type** | **Description** |
|:--------|:---------|:----------------|
| `userId` | `string` | ID of the user to operate on.  You may set this to **"@me"** to fetch the current user |
| `groupId` | `string` | The name of the group of people to fetch.  You may set this to **"@self"** to only fetch the user corresponding to `userId`, or **"@friends"** to fetch the user's friends. |
| `fields` | `array`  | An array of [profile fields](http://www.opensocial.org/Technical-Resources/opensocial-spec-v09/REST-API.html#personFields) to fetch.  You may set this to **"@all"** to fetch all available fields. |
| `count` | `number` | The maximum number of results to return. |
| `startIndex` | `number` | The starting index of the results to return (for paging). |

Additional parameters you can use are listed in the [OpenSocial specification](http://www.opensocial.org/Technical-Resources/opensocial-spec-v09/REST-API.html#standardQueryParameters).

## Response ##

The response for a successful **@self** request is an [osapiPerson](http://code.google.com/p/opensocial-php-client/source/browse/trunk/src/osapi/model/osapiPerson.php) object:

```
osapiPerson Object
(
    [displayName] => Sample Testington
    [id] => 12345
    [lookingFor] => Array
        (
            [displayValue] => Friends
            [key] => FRIENDS
        )

    [name] => Sample Testington
    [profileUrl] => http://example.com/profile/12345
    [thumbnailUrl] => http://example.com/images/people/12345.jpg
    [isOwner] => 1
    [isViewer] => 1
)
```


The response for a successful request for multiple people is an [osapiCollection](http://code.google.com/p/opensocial-php-client/source/browse/trunk/src/osapi/model/osapiCollection.php) of [osapiPerson](http://code.google.com/p/opensocial-php-client/source/browse/trunk/src/osapi/model/osapiPerson.php) objects.

```
osapiCollection Object
(
    [list] => Array
        (
            [0] => osapiPerson Object
                (
                    [displayName] => Sample Testington
                    [id] => 12345
                    [profileUrl] => http://example.com/profile/12345
                    [thumbnailUrl] => http://example.com/images/people/12345.jpg
                    [isOwner] => 
                    [isViewer] => 
                )

            [1] => osapiPerson Object
                (
                    [displayName] => Alice Testington
                    [id] => 23456
                    [profileUrl] => http://example.com/profile/23456
                    [thumbnailUrl] => http://example.com/images/people/23456.jpg
                    [isOwner] => 
                    [isViewer] => 
                )

        )

    [startIndex] => 0
    [totalResults] => 5
    [itemsPerPage] => 2
    [filtered] => 
    [sorted] => 
    [updatedSince] => 
)
```