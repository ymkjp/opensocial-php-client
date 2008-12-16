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
 */
class OpenSocialAppData implements IteratorAggregate, Countable, ArrayAccess {
  private $data;
  
  /**
   * Constructor
   */
  public function __construct($data = array()) {
    $this->data = $data;
  }
  
  public static function parseJson($data) {
    return new OpenSocialAppData($data["entry"]);
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
  
  public function offsetGet($offset) {
    if (isSet($this->data[$offset])) {
      return $this->data[$offset];
    } else {
      return null;
    }
  }
  
  public function offsetSet($offset, $value) {
    $this->items[$data] = $value;
  }
  
  public  function offsetUnset($offset) {
    unset($this->items[$data]);
  }
  
}