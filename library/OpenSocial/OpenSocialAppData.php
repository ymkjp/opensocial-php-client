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
 * Represents AppData stored on an OpenSocial container.
 * @package OpenSocial
 */
class OpenSocialAppData implements IteratorAggregate, Countable, ArrayAccess {
  private $data;
  
  /**
   * Constructor
   */
  public function __construct($data = array()) {
    $this->data = $data;
  }
  
  /**
   * Converts a JSON structure to an OpenSocialAppData instance.
   * @param mixed $data Parsed JSON.
   * @return OpenSocialAppData An initialized app data object.
   */
  public static function parseJson($data) {
    return new OpenSocialAppData($data);
  }
  
  /**
   * Converts an OpenSocialAppData instance to a data structure suitable for
   * sending to a json_encode type of function.
   * @return array An object that is JSON serializable.
   */
  public function toJsonObject() {
    return $this->data;
  }
    
  /**
   * Implements IteratorAggregate.  Allows using foreach on this class.
   */
  public function getIterator() {
    return new ArrayIterator($this->data);
  }

  /**
   * Implements Countable.  Allows using count() on this class.
   */
  public function count() {
    return count($this->data);
  }
  
  /**
   * Implements ArrayAccess.  Allows using [$index] access on this class.
   */
  public function offsetExists($offset) {
    return isSet($this->data[$offset]);
  }
  
  /**
   * Implements ArrayAccess.  Allows using [$index] access on this class.
   */
  public function offsetGet($offset) {
    if (isSet($this->data[$offset])) {
      return $this->data[$offset];
    } else {
      return null;
    }
  }
  
  /**
   * Implements ArrayAccess.  Allows using [$index] access on this class.
   */
  public function offsetSet($offset, $value) {
    $this->items[$data] = $value;
  }
  
  /**
   * Implements ArrayAccess.  Allows using [$index] access on this class.
   */
  public  function offsetUnset($offset) {
    unset($this->items[$data]);
  }
  
}