<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/core.php');
load('config.php');
load('util.php');

$db = get_db();

function _limit($limit) {
  if (!isset($limit) || !is_int($limit) || $limit < 0) {
    $limit = 60;
  }

  return $limit;
}

function _idClause($id) {
  $esc_id = mysql_real_escape_string($id);
  return "(id = '$esc_id')";
}
?>
