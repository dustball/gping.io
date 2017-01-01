<?php

interface Response {
  public function isSuccess();
  public function getError();
  public function getPayload();
  public function getResponseCode();

  public function getHeaders();
  public function getHeader($name);
  public function addHeader($name, $value);
  public function delHeaders($name);
}

class ResponseBase {
  private $code;
  private $headers;

  protected function __construct($code) {
    $this->code = $code;
    $this->headers = [];
  }

  public function getResponseCode() {
    return $this->code;
  }

  public function getHeaders() {
    return $this->headers;
  }

  public function getHeader($name) {
    $result = [];

    $cname = strtolower($name);
    foreach (array_keys($this->headers) as $k) {
      $ck = strtolower($k);
      if ($cname == $ck) {
        $result = array_merge($result, $this->headers[$k]);
      }
    }

    return $result;
  }

  public function addHeader($name, $value) {
    // TODO: validate name & value
    if (!key_exists($name, $this->headers)) {
      $this->headers[$name] = [];
    }
    $this->headers[$name][] = $value;
  }

  public function delHeaders($name) {
    $cname = strtolower($name);
    foreach (array_keys($this->headers) as $k) {
      $ck = strtolower($k);
      if ($cname == $ck) {
        unset($this->headers[$k]);
      }
    }
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
