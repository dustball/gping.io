<?php

use GPing\DB;

function get_user_for_auth($db, $email) {
  $email = mysql_real_escape_string($email);
  $query = <<<QUERY
SELECT
  id, email, password
FROM
  login_data
WHERE
  email = '$email'
LIMIT 1;
QUERY;

  return new DB\QueryResult($db, mysql_query($query, $db));
}

function get_user_by_id($db, $id) {
  $id = (int)$id;
  $query = <<<QUERY
SELECT
  id, email
FROM
  login_data
WHERE
  id = $id
LIMIT 1;
QUERY;

  return new DB\QueryResult($db, mysql_query($query, $db));
}
?>
