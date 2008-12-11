<?php

/**
 * Represents information about an OpenSocial Person account.
 */
class OpenSocialPerson {
  private $fields = null;
  
  /**
   * Constructor
   */
  public function __construct($fields = array()) {
    $this->fields = $fields;
  }
  
  /**
   * Returns the value of the requested field, if it exists on this Person.
   */
  public function getField($key) {
    if (array_key_exists($key, $this->fields)) {
      return $this->fields[$key];
    } else {
      return null;
    }
  }
  
  /**
   * Returns the ID number of this Person.
   */
  public function getId() {
    return $this->getField("id");
  }
  
  /**
   * Returns a human-readable name for this Person.
   */
  public function getDisplayName() {
    //TODO: Make names into their own class
    $name = $this->getField("name");
    $family_name = $name["familyName"];
    $given_name = $name["givenName"];
    return implode(" ", array($given_name, $family_name));
  }
    
  /**
   * Converts a JSON response containing people data into an 
   * OpenSocialCollection of OpenSocialPerson objects.
   */
  public static function parseCollectionFromJsonResponse($data) {
    $start = $data["startIndex"];
    $total = $data["totalResults"]; 
    $items = array();
    foreach ($data["entry"] as $persondata) {
      $items[] = new OpenSocialPerson($persondata);
    }
    return new OpenSocialCollection($start, $total, $items);
  }
}