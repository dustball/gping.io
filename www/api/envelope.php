<?php

interface Response {
  public function isSuccess();
  public function getError();
  public function getPayload();
  public function getResponseCode();
}

class ResponseBase {
  private $code;

  protected function __construct($code) {
    $this->code = $code;
  }

  public function getResponseCode() {
    return $this->code;
  }
}

class ApiError extends ResponseBase implements Response {
  private $err;

  public function __construct($err, $code = 500) {
    parent::__construct($code);
    $this->err = $err;
  }

  public function isSuccess() { return false; }
  public function getError() { return $this->err; }
  public function getPayload() { return null; }
}

class ApiSuccess extends ResponseBase implements Response {
  private $payload;

  public function __construct($data, $code = 200) {
    parent::__construct($code);
    $this->payload = $data;
  }

  public function isSuccess() { return true; }
  public function getError() { return null; }
  public function getPayload() { return $this->payload; }
}
?>
