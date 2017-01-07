<?php

namespace GPing;

abstract class Base implements Attempt {
  public function map(callable $fn) {
    if ($this->isFailure()) {
      return $this;
    }

    try {
      return new Success($fn($this->get()));
    } catch (Exception $e) {
      return new Failure($e);
    }
  }

  public function flatMap(callable $fn /* $data -> Attempt */) {
    $result = $this->map($fn);

    // if it's a failure there isn't anything to unwrap
    if ($result->isFailure()) {
      return $result;
    }

    // in the success case we should expect the contents to be another
    // attempt that we are going to unwrap
    $v = $result->get();
    if ($v instanceof Success) {
      return new Success($v->get());
    }
    if ($v instanceof Failure) {
      return $v;
    }

    throw new \Exception("unexpected result " . var_dump($v) . " in flatMap");
  }

  public function process(callable $succFn, callable $failFn) {
    if ($this->isSuccess()) {
      return $succFn($this->get());
    }

    if ($this->isFailure()) {
      return $failFn($this->get());
    }
  }
}

?>
