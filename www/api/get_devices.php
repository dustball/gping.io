<?php
include(dr("api/endpoint.php"));
include(dr("util.php"));
include(dr("db/auth.php"));
include(dr("db/devices.php"));

class GetDevicesApiEndpoint extends AuthedApiEndpoint implements ApiEndpoint {
  protected function authed_action($id, $path, array $path_args, $query_string) {
    if (!isset($path_args["user"])) {
      return bad_endpoint($path);
    }

    $db = get_db();
    $email = $path_args["user"];
    $result = has_email($db, $id, $email, $this->not_authed($path));

    return $result->map(function($data) use ($db, $id) {
      return get_devices($db, $id)->process(
        function($query_result) {
          $device_data = [];

          while ($row = $query_result->row()) {
            $device_data[] = [
              "device_id" => $row["device_id"],
              "device_type_id" => $row["device_type_id"],
              "device_type_name" => $row["name"],
              "group_id" => $row["group_id"]
            ];
          }
          return new ApiSuccess(["devices" => $device_data]);
        },

        function($err) {
          return internal_server_error($err); 
        }
      );
    })->get();
  }
}

?>
