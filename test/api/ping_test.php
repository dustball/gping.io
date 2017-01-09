<?php

$_SERVER = Array();
$_SERVER["DOCUMENT_ROOT"] = "/var/www/html";

require_once "PHPUnit/Autoload.php";
require_once "/var/www/html/core.php";
require_once "/var/www/html/api/ping.php";
require_once "/var/www/html/api/envelope.php";

class api_ping_test extends PHPUnit_Framework_TestCase {

  public function test_ping() {
    $ping = new PingApiEndpoint();
    $response = $ping->act("/",Array(),"");
    $this->assertEquals($response->getPayload(),"pong");
    $this->assertTrue($response->isSuccess());
  }

  public function test_ping_api() {
    $json = file_get_contents("http://localhost:80/api/v1/ping");
    $o = json_decode($json);
    $this->assertEquals($o->status,"ok");
    $this->assertNull($o->error);
    $this->assertEquals($o->data, "pong");
  }
}

?>
