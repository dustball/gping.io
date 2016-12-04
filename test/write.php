<?php
require('_env.php');

function req($id, $ver, $args = []) {
  if ($id != null) { $args['id'] = $id; }
  if ($ver != null) { $args['ver'] = $ver; }

  $arg_str = http_build_query($args);
  $result = `curl -s 'localhost:80/write.php?$arg_str'`;
  return json_decode($result, true);
}

function last_addition($tbl) {
  $r = mysql_query("select * from $tbl order by t desc limit 1");
  return mysql_fetch_assoc($r);
}

function get_count($tbl) {
  $r = mysql_query("select count(*) from $tbl");
  return mysql_fetch_array($r)[0];
}

function get_counts() {
  return [
    'gping' => get_count('gping'),
    'gping_gloc' => get_count('gping_gloc'),
    'gping_nloc' => get_count('gping_nloc')
  ];
}

function check_entries($counts, $tbls) {
  foreach ($counts as $t => $val) {
    $now_cnt = get_count($t);
    $want_cnt = $counts[$t];

    if (in_array($t, $tbls)) {
      $want_cnt++;
    }

    if ($now_cnt != $want_cnt) {
      return "wanted $want_cnt entries in $t; got $now_cnt";
    }
  }

  return false;
}

function del_last($tbl) {
  $r = mysql_query("delete from $tbl order by t desc limit 1");
}

function is_resp($r) {
  return $r != null;
}

function not_error($r) {
  return !isset($r['error']);
}

function is_error($r) {
  return isset($r['error']);
}

function has_id($r) {
  global $good_id;
  return isset($r['id']) && $r['id'] == $good_id;
}

function expect($name, $fn, $r) {
  if ($fn($r)) { return; }
  echo "$name failed\n"; 
}

$tstamp = "2016-12-04 20:56:15";
$good_id = '012345678901';
$ver = '-1';

function test_loc_only($type, $data, $expect_err) {
  global $good_id;
  global $ver;
  $counts = get_counts();
  $result = req($good_id, $ver, $data);
  expect("is json response", is_resp, $result);
  expect("has expected id", has_id, $result);
  if ($err = check_entries($counts, ["gping_$type"."loc"])) {
    echo "$err\n";
  }
}

test_loc_only(
  'g',
  ['glat' => 1, 'glng' => 2, 'gtime' => $tstamp, 'gaccuracy' => 3],
  false
);
  


$result = req('',2);
print_r($result);
?>
