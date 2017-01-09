<?php
include(dr("api/endpoint.php"));
include(dr("util.php"));
include(dr("db/auth.php"));
include(dr("db/devices.php"));

class NewDeviceApiEndpoint extends AuthedApiEndpoint implements ApiEndpoint {
  protected function authed_action($id, $path, array $path_args, $query_string) {
    if (!isset($path_args["user"])) {
      return bad_endpoint($path);
    }

    $db = get_db();
    $email = $path_args["user"];
    $result = has_email($db, $id, $email, $this->not_authed($path));

    return $result->map(function($data) use ($db, $id) {
      return create_vehicle($db, $id)->process(
        function($dev_id) {
          return new ApiSuccess(["device_id" => $dev_id]);
        },
        function($err) {
          return internal_server_error($err);
        }
      );
    })->get();
  }
}

?>
