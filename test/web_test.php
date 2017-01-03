<?php

require_once "PHPUnit/Autoload.php";

class web_test extends PHPUnit_Framework_TestCase {
  
  public function test_website() {
    $html = file_get_contents("http://localhost:80");
    $live_demo = strpos($html,"Live Demo") > 0;
    $this->assertTrue($live_demo);
  } 

  public function test_live_demo() {
    $html = file_get_contents("http://localhost:80/13iakz-u9i6u9");
    $live_demo = strpos($html,"initMap()") > 0;
    $this->assertTrue($live_demo);
  } 
  
}