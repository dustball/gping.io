<?php
//require_once($_SERVER["DOCUMENT_ROOT"] . '/core.php');
//load('db/db.php');

// Records a location
function insert($table, $id, $values) {
  global $db;

  if (!isset($table) || $table == "") {
    return "no destination provided";
  }

  if (!isset($id) || $id == "") {
    return "valid id must be provided";
  }

  $elem_cnt = count($values);
  if ($elem_cnt < 1) {
    // not technically an error since inserting nothing is trivial...
    return false;
  }

  $names = [];
  $args = [];

  foreach ($values as $key => $val) {
    $content = "";
    if (is_array($val)) {
      if ($val[1]) {
        $content = "'".mysql_real_escape_string($val[0])."'";
      } else {
        $content = $val[0];
      }
    } else {
      $content = $val;
    }

    array_push($args, $content);
    array_push($names, $key);
  }

  $names = implode(",", $names);
  $args = implode(",", $args);

  $stmt = "INSERT INTO $table ($names) VALUES ($args)";

  if (!$mysql_query($stmt)) {
    return mysql_error();
  }
  return false;
}

$gps = 1;
$net = 2;
$table = array($gps => 'gping_gloc', $net => 'gping_nloc');

function q($s) { return [$s, true]; }

function write_loc($type, $id, $t, $lat, $lng, $acc = 0) {
  global $table;

  $errs = [];

  $t = $t + 0;
  if (!is_long($t)) {
    array_push($errs, "reported time must be a timestamp");
  }
  $t = "from_unixtime(" . (((int)$t) / 1000) . ")";

  $lat = $lat + 0;
  if (!is_numeric($lat)) {
    array_push($errs, "latitude must be a number");
  }

  $lng = $lng + 0;
  if (!is_numeric($lng)) {
    array_push($errs, "longitude must be a number");
  }

  $acc = $acc + 0;
  if (!is_numeric($acc)) {
    array_push($errs, "location accuracy must be a number");
  }

  if (count($errs) != 0) {
    return $errs;
  }

  $insert_result = insert($table[$type], $id, array(
    'id' => q($id),
    't' => 'now()',
    'time' => $t,
    'lat' => $lat,
    'lng' => $lng,
    'accuracy' => $acc
  ));

  if ($insert_result) {
    return [$insert_result];
  }

  return false;
}

function write_gloc($id, $t, $lat, $lng, $acc = 0) {
  global $gps;
  write_loc($gps, $id, $t, $lat, $lng, $acc);
}

function write_nloc($id, $t, $lat, $lng, $acc = 0) {
  global $net;
  write_loc($net, $id, $t, $lat, $lng, $acc);
}
?>
