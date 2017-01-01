<?php
include(dr("api/endpoint.php"));

class VerifyApiEndpoint extends AuthedApiEndpoint {
  protected function authed_action($id, $path, array $named_args, $query_string) {
    return new ApiSuccess("successfully logged in as user id '$id' accessing $path");
  }
}
?>
