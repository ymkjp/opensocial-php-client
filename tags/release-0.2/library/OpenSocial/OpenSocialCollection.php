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
 * Represents a collection of OpenSocial objects.  Can be iterated over.
 * @package OpenSocial
 */
class OpenSocialCollection implements 
    IteratorAggregate, Countable, ArrayAccess {
  public $startIndex = 0;
  public $totalResults = 0;
  private $items = null;

  /**
   * Constructor
   */
  public function __construct($start = 0, $total = 0, $items = array()) {
    $this->startIndex = $start;
    $this->totalResults = $total;
    $this->items = $items;
  }
  
  /**
   * Implements IteratorAggregate.  Allows using foreach on this class.
   */
  public function getIterator() {
    return new ArrayIterator($this->items);
  }

  /**
   * Implements Countable.  Allows using count() on this class.
   */
  public function count() {
    return count($this->items);
  }
  
  /**
   * Implements ArrayAccess.  Allows using [$index] access on this class.
   */
  public function offsetExists($offset) {
    return isSet($this->items[$offset]);
  }
  
  /**
   * Implements ArrayAccess.  Allows using [$index] access on this class.
   */
  public function offsetGet($offset) {
    if (isSet($this->items[$offset])) {
      return $this->items[$offset];
    } else {
      return null;
    }
  }
  
  /**
   * Implements ArrayAccess.  Allows using [$index] access on this class.
   */
  public function offsetSet($offset, $value) {
    $this->items[$offset] = $value;
  }
  
  /**
   * Implements ArrayAccess.  Allows using [$index] access on this class.
   */
  public  function offsetUnset($offset) {
    unset($this->items[$offset]);
  }
}
