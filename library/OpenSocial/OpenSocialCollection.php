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
 */
class OpenSocialCollection implements IteratorAggregate, Countable {
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
   * Part of the IteratorAggregate interface.  Allows using foreach on this
   * class.
   */
  public function getIterator() {
    return new ArrayIterator($this->items);
  }

  /**
   * Part of the Countable interface.  Allows using count() on this class.
   */
  public function count() {
    return count($this->items);
  }
}
