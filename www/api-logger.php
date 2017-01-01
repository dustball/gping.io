<?php

function api_logger($errno, $errstr, $errfile, $errline, array $errctx) {
  // lol, do better
  if (($errno == E_ERROR) || ($errno == E_WARNING)) {
    throw new Exception($errstr);
  }
}

?>
