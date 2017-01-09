<?php

$_SERVER = Array();
$_SERVER["DOCUMENT_ROOT"] = "/var/www/html";

require_once "PHPUnit/Autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/core.php";
require_once __DIR__ . "/common.php";

class api_verify_signin_test extends PHPUnit_Framework_TestCase {
  public function test_success() {
    $resp = get("api/v1/verify", [
      "data" => null,
      "headers" => ["Authorization: " . Fixtures::$user1_token]
    ]);

    api_okay($resp, $this);
  }

  public function test_bad_token() {
    $resp = get("api/v1/verify", [
      "data" => null,
      "headers" => ["Authorization: bad_token"]
    ]);

    api_error($resp, $this, 403);
  }

  public function test_no_token() {
    $resp = get("api/v1/verify");

    api_error($resp, $this, 403);
  }
}

?>
