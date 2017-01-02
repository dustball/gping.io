<?php
require_once "PHPUnit/Autoload.php";

class web_test extends PHPUnit_Framework_TestCase {
  
      public function test_website() {
        $html = file_get_contents("http://localhost:80");
        $live_demo = strpos($html,"Live Demo") > 0;
        $this->assertTrue($live_demo);
    }       
    
}