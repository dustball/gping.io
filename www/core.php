<?php

function dr($p) {
  return $_SERVER["DOCUMENT_ROOT"] . "/$p";
}

// TODO: remove usage of this
function load($name) {
  require_once($_SERVER["DOCUMENT_ROOT"] . "/$name");
}

require_once(dr('vendor-composer/autoload.php'));

function _method($m) { return $_SERVER["REQUEST_METHOD"] == $m; }
function _isPost()   { return _method("POST"); }
function _isGet()    { return _method("GET"); }
function _isPut()    { return _method("PUT"); }
function _isDelete() { return _method("DELETE"); }

function _getPostBody() {
  // 10k max content
  // TODO: move to configurable place
  $maxSize = 10 * 1024;

  if (!_isPost()) {
    return "";
  }

  $input = fopen("php://input", 'r');
  if (!$input) {
    return "";
  }

  $body = stream_get_contents($input, $maxSize + 1);
  fclose($input);

  if (FALSE === $body || strlen($body) > $maxSize) {
    return "";
  }

  return $body;
}

function _getBodyJSON() {
  $body = _getPostBody();
  if ($body == "") {
    return false;
  }

  return json_decode($body, true);
}
?>
