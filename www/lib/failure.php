<?php

namespace GPing;

class Failure extends Base {
  private $ex;

  public function __construct($ex) {
    $this->ex = $ex;
  }

  public function isSuccess() { return false; }
  public function isFailure() { return true; }
  public function get() { return $this->ex; }
}

?>
