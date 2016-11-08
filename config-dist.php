<?php

// Copy this file to config.php and make changes there

function config_db() {
    // hostname, username, password
    return mysql_pconnect('database.hostname', 'username', 'password');
}

function config_db_name($link) {
    // database name
    return mysql_select_db('dbname', $link);
}

function get_demo_id() {
    // demo ID
    return "13iakz-u9i6u9";
}

function get_demo_vin() {
    // To obscure the demo vehicle's VIN (optional)
    return "12345678912345678";
}

function get_gmap_key() {
    // See https://developers.google.com/maps/documentation/javascript/get-api-key#key
    return "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
}

?>

