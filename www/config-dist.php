<?php

// Copy this file to config.php and make changes there.

function env($name, $default) {
  $e = $_ENV[$name];
  if (!isset($e)) {
    $e = $default;
  }

  return $e;
}

function config_db() {
  $db_host = env('GPING_DB_HOST', 'database.hostname');
  $db_user = env('GPING_DB_USER', 'username');
  $db_pass = env('GPING_DB_PASS', 'password');

  // hostname, username, password
  return mysql_pconnect($db_host, $db_user, $db_pass);
}

function config_db_name($link) {
  $db_name = env('GPING_DB_NAME', 'dbname');
  // database name
  return mysql_select_db($db_name, $link);
}

function get_demo_id() {
  // demo ID
  $demo_id = env('GPING_DEMO_ID', "xxxxx-xxxxxx");
  return $demo_id;
}

function get_demo_vin() {
  // To obscure the demo vehicle's VIN (optional)
  return env('GPING_DEMO_VIN', "12345678912345678");
}

function get_gmap_key() {
  // See https://developers.google.com/maps/documentation/javascript/get-api-key#key
  return env('GPING_GMAP_API_KEY', "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
}

?>
