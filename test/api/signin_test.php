<?php

$_SERVER = Array();
$_SERVER["DOCUMENT_ROOT"] = "/var/www/html";

require_once "PHPUnit/Autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/core.php";
require_once __DIR__ . "/common.php";

class api_signin_test extends PHPUnit_Framework_TestCase {
  public function test_success() {
    $resp = post_encode("api/v1/signin", [
      "username" => "user1@example.com",
      "password" => "user1-password"
    ]);

    $t = $this;
    api_okay($resp, $this)->map(function($r) use ($t) {
      $o = $r[2];
      $t->assertNotEmpty($o->data->token);
    });
  }

  public function test_bad_password() {
    $resp = post_encode("api/v1/signin", [
      "username" => "user1@example.com",
      "password" => "bad-password"
    ]);

    $t = $this;
    api_error($resp, $this, 403)->map(function($r) use ($t) {
      $o = $r[2];
      $t->assertEmpty($o->data);
    });
  }

  public function test_no_user() {
    $resp = post_encode("api/v1/signin", [
      "password" => "user1-password"
    ]);

    $t = $this;
    api_error($resp, $this, 403)->map(function($r) use ($t) {
      $o = $r[2];
      $t->assertEmpty($o->data);
    });
  }

  public function test_bad_post_data() {
    $resp = post("api/v1/signin", "[asoentuh");

    $t = $this;
    api_error($resp, $this, 403)->map(function($r) use ($t) {
      $o = $r[2];
      $t->assertEmpty($o->data);
    });
  }

  public function test_http_get() {
    $resp = get("api/v1/signin");

    $t = $this;
    api_error($resp, $this, 405)->map(function($r) use ($t) {
      $o = $r[2];
      $t->assertEmpty($o->data);
    });
  }
}

?>
