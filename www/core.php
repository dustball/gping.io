<?php

function dr($p) {
  return $_SERVER["DOCUMENT_ROOT"] . "/$p";
}

function lib($p) {
  return dr("lib/$p");
}

// TODO: remove usage of this
function load($name) {
  require_once($_SERVER["DOCUMENT_ROOT"] . "/$name");
}

require_once(dr('vendor-composer/autoload.php'));

function _isMethod($m) { return $_SERVER["REQUEST_METHOD"] == $m; }
function _isPost()   { return _isMethod("POST"); }
function _isGet()    { return _isMethod("GET"); }
function _isPut()    { return _isMethod("PUT"); }
function _isDelete() { return _isMethod("DELETE"); }

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

function _snake($s) {
  return strtolower(
    preg_replace(
      ['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'],
      '$1_$2',
      $s));
}

function _camel($s) {
  return str_replace(' ', '', ucwords(str_replace('_', ' ', $s)));
}

function find_gping_lib($name) {
  if (strpos($name, "GPing\\") !== 0) {
    return false;
  }

  $pcs = explode('\\', $name);

  // drop GPing
  array_shift($pcs);
  // put remainder in canonical format
  for ($i = 0; $i < count($pcs); $i++) {
    $pcs[$i] = _snake($pcs[$i]);
  }

  // and construct the path!
  $path = implode(DIRECTORY_SEPARATOR, $pcs);
  include(lib("$path.php"));
}

spl_autoload_register(find_gping_lib);
?>
