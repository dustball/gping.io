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
?>
