<?php

$_SERVER = Array();
$_SERVER["DOCUMENT_ROOT"] = "/var/www/html";

require_once "PHPUnit/Autoload.php";

class ping_test extends PHPUnit_Framework_TestCase {

  public function test_ping_api() {
    $json = file_get_contents("http://localhost:80/ping?ver=1");
    $o = json_decode($json);
    $this->assertEquals($o->result,"ok");
    $this->assertEquals(strlen($o->id),12);
  }
}

?>
