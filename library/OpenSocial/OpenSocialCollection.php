<?php

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
