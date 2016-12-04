<?php

// check an associative array for $from key and, if $to is not already set,
// update the value to live at $h.
function rewrite_arr(&$data, $from, $to) {
  if (!isset($data[$to]) && isset($data[$from])) {
    $data[$to] = $data[$from];
    unset($data[$from]);
  }
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function get_db() {
  $link = config_db();
  if (!$link) {
    die('Could not connect #200: ' . mysql_error());
  }
  $db_selected = config_db_name($link);
  if (!$db_selected) {
    die('Can\'t use db #200: ' . mysql_error());
  }
  return $link;
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function hourminute($t_str) {
  $dt = new DateTime($t_str);
  $dt->format('H.i');
}
?>
