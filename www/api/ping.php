<?php
include(dr("api/endpoint.php"));

class PingApiEndpoint implements ApiEndpoint {
  function act(array $named_args, $query_string) {
    return new ApiSuccess("pong");
  }
}

?>
