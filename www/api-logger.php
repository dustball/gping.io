<?php

function api_logger($errno, $errstr, $errfile, $errline, array $errctx) {
  // lol, do better
  switch ($errno) {
    case E_ERROR:
    case E_WARNING:
    case E_PARSE:
    case E_CORE_ERROR:
    case E_CORE_WARNING:
    case E_COMPILE_ERROR:
    case E_COMPILE_WARNING:
      throw new Exception($errstr);
  }
}

?>
