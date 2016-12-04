<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/core.php');
load('db/db.php');

// Records a row, $table and $id are required.
// Returns either false or an error message.
//
// $values is 'field name' => (value | [$value, should quote & escape])
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

  $names = ['id'];
  $args = ["'".mysql_real_escape_string($id)."'"];

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

  if (!mysql_query($stmt)) {
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

// extracts $name from $src or returns false; if a value is found will verify
// validity using $type_fn (x => [error_bool, alteredx | error string]) and
//
// if $name is not set returns false otherwise returns [error_bool, insertable | error string]
function get_value($src, $name, $type_fn) {
  $v = $src[$name];
  if (!isset($v)) {
    return false;
  }

  $check = $type_fn($v);
  if ($check[0]) {
    return $check;
  }

  return [false, $check[1]];
}

function check_pass($x) {
  return [false, $x];
}

function write_obds($id, $ver, $data) {
  $names = [];
  $values = [];
  $insert_values = [
    'ver' => q($ver),
    't'   => 'now()'
  ];

  $fields = [
    'bat_percent',
    'bat_change',
    'bat_status',
    'account',
    'aid',
    'fleet_id',
    'locked',
    'obds',
    'uptime_phone',
    'uptime_app',
    'voltage'
  ];

  foreach ($fields as $f) {
    if ($v = get_value($data, $f, check_pass)) {
      if ($v[0]) {
        // bail on first error
        return $v[1];
      }
      $insert_values[$f] = q($v[1]);
    }
  }

  // TODO: we could bail here but potentially there is utility in receiving
  // a ping even if it carries no data?
  // if (count($insert_values) == 0) {
  //   return false;
  // }
  $insert_values['ver'] = q($ver);
  $insert_values['t'] = 'now()';

  rewrite_arr($insert_values, 'fleet_id', 'fleetid');

  // the typo lives on in schema soooooo... back we go
  rewrite_arr($insert_values, 'obds', 'odbs');
  // eventually we can fix this by
  //  1. altering table to have both columns
  //  2. updating insert code to write both
  //  3. updating read code to read both and prefer newer value if not null
  //  3. updating write column to write only the newest
  //  4. move data from old column to new
  //  5. drop old column.
  return insert('gping', $id, $insert_values);
}
?>
