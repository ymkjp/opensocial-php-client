<?php
/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
 
/**
 * OpenSocial Client Library for PHP
 * 
 * @package OpenSocial
 */
 
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
   * Returns whether this person is the current viewer (xoauth_requestor_id).
   */
  public function isViewer() {
    return $this->getField("isViewer") == 1;
  }
  
  /**
   * Returns a string representation of this person.
   */
  public function __toString() {
    return sprintf("%s [%s]", $this->getDisplayName(), $this->getId());
  }
    
  /**
   * Converts a JSON response containing a single person's data into an
   * OpenSocialPerson object.
   */
  public static function parseJson($data) {
    return new OpenSocialPerson($data["entry"]);
  }
  
  /**
   * Converts a JSON response containing people data into an 
   * OpenSocialCollection of OpenSocialPerson objects.
   */
  public static function parseJsonCollection($data) {
    $start = $data["startIndex"];
    $total = $data["totalResults"]; 
    $items = array();
    foreach ($data["entry"] as $persondata) {
      $items[] = new OpenSocialPerson($persondata);
    }
    return new OpenSocialCollection($start, $total, $items);
  }
}