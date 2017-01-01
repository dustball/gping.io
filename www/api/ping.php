<?php
include(dr("api/endpoint.php"));

class PingApiEndpoint implements ApiEndpoint {
  public function act($path, array $named_args, $query_string) {
    return new ApiSuccess("pong");
  }
}

?>
