<?php
include(dr("api/endpoint.php"));

class NewDeviceApiEndpoint extends AuthedApiEndpoint implements ApiEndpoint {
  protected function authed_action($id, $path, array $named_args, $query_string) {
    return new ApiError([
      "short" => "NOT IMPLEMENTED",
      "args" => $named_args,
      "qs" => $query_string
    ], 501);
  }
}

?>
