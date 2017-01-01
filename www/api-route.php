<?php
  include($_SERVER["DOCUMENT_ROOT"] . "/core.php");
  include(dr('api/envelope.php'));
  include(dr('api-logger.php'));

  // constructs a v1 API route $p
  function v1($p) { return "/v1/$p"; };

  // routing_table constructs the API endpoints and configures their handlers
  $routing_table = function(FastRoute\RouteCollector $r) {
    $r->get(v1('ping'), mk_handler('ping'));
    $r->post(v1('signin'), mk_handler('auth'));
    $r->get(v1('verify'), mk_handler('verify'));
  };

  // render produces a HTTP response out of an API response. This includes
  // sending the appropriate respone code and headers as well as JSON encoding
  // the error or payload objects.
  function render(Response $r) {
    http_response_code($r->getResponseCode());

    if (count($r->getHeader("Content-Type")) == 0) {
      header("Content-Type: application/json");
    }

    // write headers
    $hs = $r->getHeaders();
    if ($hs != null) {
      foreach (array_keys($hs) as $k) {
        foreach ($hs[$k] as $v) {
          header("$k: $v");
        }
      }
    }

    $resp = [
      "status" => $r->isSuccess() ? "ok" : "error",
      "error" => $r->getError(),
      "data" => $r->getPayload()
    ];

    echo(json_encode($resp, JSON_PRETTY_PRINT) . "\n");
  }

  // uri_path extracts the path from a URI
  function uri_path($uri) {
    if (false !== $pos = strpos($uri, '?')) {
      $uri = substr($uri, 0, $pos);
    }

    if (0 == strpos($uri, "/api")) {
      $uri = substr($uri, 4);
    }

    return $uri;
  }

  // uri_query extracts the query string from a URI (does not include the '?'
  // delimiter)
  function uri_query($uri) {
    if (false !== $pos = strpos($uri, '?')) {
      return substr($uri, $pos + 1);
    }
    return "";
  }

  // mk_handler returns a closure (so that we defer execution until we know it
  // is necessary) that:
  //   1. builds the path for the relevant handler & includes that definition
  //   2. constructs a newly included handler
  //   3. runs the handler providing path-exracted args and the row query string
  function mk_handler($handler_name, $camel_name = null) {
    return function($path, array $named_args, $query_string) use ($handler_name, $camel_name) {
      $handler_path = dr("api/$handler_name.php");
      include($handler_path);

      if ($camel_name == null) {
        $camel_name = _camel($handler_name);
      }

      $ctrl = null;
      eval('$ctrl = new ' . $camel_name . 'ApiEndpoint();');
      return $ctrl->act($path, $named_args, $query_string);
    };
  }

  // route will handle the request and return the API Response object produced
  // by the handler it found (or one produced internally in an error case).
  function route() {
    global $routing_table;
    $router = FastRoute\simpleDispatcher($routing_table);

    $path = uri_path($_SERVER["REQUEST_URI"]);
    $query = uri_query($_SERVER["REQUEST_URI"]);
    $method = $_SERVER["REQUEST_METHOD"];
    $dispatchResult = $router->dispatch($method, $path);

    $apiResult = null;

    switch ($dispatchResult[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
      $apiResult = new ApiError("API endpoint '$path' is unrecognized", 404);
      break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
      $apiResult = new ApiError("'$method' is not allowed on API endpoint '$path'", 405);
      foreach ($dispatchResult[1] as $allowed_method) {
        $apiResult->addHeader("Allowed", $allowed_method);
      }
      break;

    case FastRoute\Dispatcher::FOUND:
      $fn = $dispatchResult[1];
      $named_args = $dispatchResult[2];
      $apiResult = $fn($path, $named_args, $query);
      break;
    }

    return $apiResult;
  }

  set_error_handler(api_logger);
  try {
    render(route());
  } catch (Exception $e) {
    render(new ApiError("Internal Server Error", 503));
  }
?>
