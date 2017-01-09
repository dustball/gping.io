<?php

namespace GPing;

class Success extends Base {
  private $value;

  public function __construct($v = null) {
    $this->value = $v;
  }

  public function isSuccess() { return true; }
  public function isFailure() { return false; }
  public function get() { return $this->value; }
}

?>
