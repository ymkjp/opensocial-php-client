Once you have [connected the library to a container](HowToConnecting.md) and obtained an `osapi` instance, you can start making calls against the OpenSocial APIs.



# Requests #
Calls to the container are treated as individual request objects by the client library.  For example, here's code that creates a simple request for the current viewer:

```
$request = $osapi->people->get(array('userId'=>'@me', 'groupId'=>'@self'));
```

# Batching #
Batching your requests to the OpenSocial APIs will help the performance of your application.  This library supports batch requests by creating a batch object and adding requests to it:

```
$batch = $osapi->newBatch();
$batch->add($request, 'request_label');
```

Requests must have unique labels which will be used to access the results of each individual request later.  To get the results, run the batch's `execute` method:

```
$result = $batch->execute();
```

# Processing Results #
The batch result object comes back as an associative array where they keys are the labels you assigned when adding requests to the batch, and the values are the data returned by the container.  You can therefore access the result of a specific operation like this:

```
$result_item = $result['request_label'];
```

If you'd like to iterate over each result item, then you can do so like this:

```
foreach ($result as $key => $result_item) {
  //Process the response item here
}
```

# Handling errors #
Response items which result in a failed request will be instances of `osapiError` objects.  Errors can result from failed HTTP connections, attempting to perform an unsupported operation on the container, or even being over quota for a certain type of operation.  Therefore, you should check for an error when handling every response from the client library.  You can determine whether a response is an error with the following code:

```
if ($result_item instanceof osapiError) {
  //Process error response
} else {
  //Process valid response
}
```

`osapiError` objects have two methods which you can use to obtain more information about the error which occurred, `getErrorCode` and `getErrorMessage`.

```
if ($result_item instanceof osapiError) {
  $error_code = $result_item->getErrorCode();
  $error_text = $result_item->getErrorMessage();
  //Handle the error here
} else {
  //Process valid response
}
```