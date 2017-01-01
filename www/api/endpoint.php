<?php

// ApiEndpoint is the expected interface each endpoint for the API will implement.
interface ApiEndpoint {
  // act performs the actions associated with this endpoint.
  // it must return a Response implementation.
  function act(array $namedArgs, $queryString) /*: Response */;
}

?>
