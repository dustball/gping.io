<?php

use GPing\DB;

function get_user($db, $email) {
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

?>
