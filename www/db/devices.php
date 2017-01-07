<?php

use GPing\Success;
use GPing\Failure;
use GPing\DB;

function create_device($db, $user_id, $device_type) {
  $id = generateRandomString(12);

  $query = <<<QUERY
INSERT INTO user_devices(device_id, user_id, device_type_id) VALUES
  ('$id', $user_id, $device_type);
QUERY;

  if (!mysql_query($query)) {
    return new Failure(mysql_error());
  }

  return new Success($id);
}

// create_vehicle adds a new vehicle type for the specified $user_id. On success
// a GPing\Success is returned with the new device id. Otherwise a GPing\Failure
// is returned with the db error.
function create_vehicle($db, $user_id) {
  return create_device($db, $user_id, 1);
}

// get_devices returns all devices known for the specified user. On success a
// GPing\Success(GPing\DB\QueryResult) is returned. On failure a GPing\Failure
// is returned with the error pulled from the mysql_query call.
function get_devices($db, $user_id) {
  $query = <<<QUERY
SELECT
  d.device_id, d.device_type_id, dt.name, d.group_id
FROM
  user_devices d,
  device_type dt
WHERE
  d.device_type_id = dt.device_type_id
;
QUERY;

  $result = new DB\QueryResult($db, mysql_query($query, $db));

  if ($result->err()) {
    return new Failure($result->err());
  } else {
    return new Success($result);
  }
}
?>
