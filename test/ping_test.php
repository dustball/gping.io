<?php

$_SERVER = Array(); 
$_SERVER["DOCUMENT_ROOT"] = "/var/www/html";

require_once "PHPUnit/Autoload.php";
require_once "/var/www/html/core.php";
require_once "/var/www/html/api/ping.php";
require_once "/var/www/html/api/envelope.php";

class ping_test extends PHPUnit_Framework_TestCase {
  
  public function test_ping() {
    $ping = new PingApiEndpoint();
    $response = $ping->act("/",Array(),"");
    $this->assertEquals($response->getPayload(),"pong");
    $this->assertTrue($response->isSuccess());
  } 

  public function test_ping_api() {
    $json = file_get_contents("http://localhost:80/ping?ver=1");
    $o = json_decode($json);
    $this->assertEquals($o->result,"ok");
    $this->assertEquals(strlen($o->id),12);
  } 
        
}