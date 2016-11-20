<?php

function load($name) {
  require_once($_SERVER["DOCUMENT_ROOT"] . "/${name}");
}

?>
