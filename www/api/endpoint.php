<?php

// ApiEndpoint is the expected interface each endpoint for the API will implement.
interface ApiEndpoint {
  // act performs the actions associated with this endpoint. It must return a
  // Response implementation.
  public function act($path, array $named_args, $query_string) /*: Response */;
}

?>
