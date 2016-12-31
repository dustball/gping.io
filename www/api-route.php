<?php
  include($_SERVER["DOCUMENT_ROOT"] . "/core.php");
  include(dr('api/envelope.php'));

  function api($n) {
    return dr("api/$n.php");
  }

  function render(Response $r) {
    http_response_code($r->getResponseCode());

    $resp = [
      "status" => $r->isSuccess() ? "ok" : "error",
      "error" => $r->getError(),
      "data" => $r->getPayload()
    ];

    echo(json_encode($resp, JSON_PRETTY_PRINT));
  }

  // TODO: replace with a real dispatcher; or write a simple-but-shitty one
  // https://github.com/nikic/FastRoute looks promising
  $headVersion = "v0";
  $routes = [
    "v0" => ["ping" => "ping"]
  ];

  $path = $_GET["path"];
  $pcs = explode('/', $path);

  if (key_exists($pcs[0], $routes)) {
    $versionedRoutes = $routes[$pcs[0]];
    $path = $pcs[1];
  } else {
    $versionedRoutes = $routes[$headVersion];
  }

  if (key_exists($path, $versionedRoutes)) {
    include(api($versionedRoutes[$path]));
  }

  if (function_exists('act')) {
    $apiResult = act();
  } else {
    $apiResult = new ApiError("$path is unrecognized", 404);
  }

  render($apiResult);
?>
