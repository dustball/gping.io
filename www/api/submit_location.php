<?php
include(dr("api/endpoint.php"));
include(dr("util.php"));
include(dr("db/auth.php"));
include(dr("db/devices.php"));

class SubmitLocationApiEndpoint extends AuthedApiEndpoint implements ApiEndpoint {
  protected function authed_action($id, $path, array $path_args, $query_string) {
    if (!isset($path_args["user"]) || !isset($path_args["device_id"])) {
      return bad_endpoint($path);
    }

    $db = get_db();
    $email = $path_args["user"];
    $device_id = $path_args["device_id"];

    $result = has_email($db, $id, $email, $this->not_authed($path));

    return $result->map(function($user) use ($device_id) {

    });
  }
}
?>
