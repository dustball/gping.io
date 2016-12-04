<?php

/*
 * Store an incoming ping.
 */

require_once('core.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/util.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/db/db_write.php');

// for the time being make json pretty
$encode_opts = JSON_PRETTY_PRINT;

// There was a typo in the android app for a bit so rewrite parameters
// for "odbs" to the correct "obds"
rewrite_arr($_REQUEST, 'odbs', 'obds');

function update($r) {
  $resp = array('result' => '');

  $id = $r['id'];

  // If no id is present assume this is a new user and construct one for them
  if (strlen($id) != 12) {
    $id = generateRandomString(12);
  }
  $resp['id'] = $id;

  if (isset($r['ver'])) {
    if (isset($r['glat'])) {
      if ($err = write_gloc($id, $r['gtime'], $r['glat'], $r['glng'], $r['gaccuracy'])) {
        $resp['error'] = $err;
        return $resp;
      }
    }

    if (isset($r['nlat'])) {
      if ($err = write_nloc($id, $r['ntime'], $r['nlat'], $r['nlng'], $r['naccuracy'])) {
        $resp['error'] = $err;
        return $resp;
      }
    }

    if ($err = write_obds($id, $r['ver'], $r)) {
      $resp['error'] = $err;
    }

    $resp['result'] = "ok";
  } else {
    $resp['error'] = "Required attribute 'ver' was not provided";
  }

  return $resp;
}

print(json_encode(update($_REQUEST), $encode_opts));
?>
