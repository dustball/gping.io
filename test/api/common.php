<?php
  use GPing\Attempt;
  use GPing\Success;
  use GPing\Failure;

  include_once(__DIR__ . "/fixtures.php");

  // Supported opts keys:
  //   data - data that should be included in the body
  //   headers - array of header strings in the format ["HeaderName: Value", "HeaderName2: Value2"]
  function default_opts(array $opts = null) {
    if ($opts == null) {
      $opts = [];
    }

    $keys = [
      "data",
      "headers"
    ];

    foreach ($keys as $k) {
      if (!array_key_exists($k, $opts)) {
        $opts[$k] = null;
      }
    }

    return $opts;
  }

  function post_encode($url, array $data, array $opts = null) {
    $data = json_encode($data);
    return post($url, $data);
  }

  function post($url, $data, array $opts = null) {
    $opts['data'] = $data;
    return _req(CURLOPT_POST, $url, $opts);
  }

  function get($url, array $opts = null) {
    return _req(CURLOPT_HTTPGET, $url, $opts);
  }

  function _req($typ, $url, array $opts = null) {
    $opts = default_opts($opts);
    $data = $opts["data"];
    $headers = $opts["headers"];

    $resource = curl_init("http://localhost:80/$url");
    curl_setopt($resource, $typ, TRUE);
    if ($data != null) {
      curl_setopt($resource, CURLOPT_POSTFIELDS, $data);
    }
    if ($headers != null) {
      curl_setopt($resource, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($resource, CURLOPT_RETURNTRANSFER, TRUE); 
    $response = curl_exec($resource);

    $result = null;
    if (!$response) {
      $result = new Failure(curl_error($resource));
    } else {
      $code = curl_getinfo($resource, CURLINFO_HTTP_CODE);
      $result = new Success([$code, $response]);
    }
    curl_close($resource);

    return $result;
  }

  function api_okay(Attempt $req_response, $test, $want_code = 200) {
    if ($req_response->isFailure()) {
      $test->assertTrue(false);
      return new Failure();
    }

      return $req_response->map(function($r) use ($test, $want_code) {
          $code = $r[0];
          $body = $r[1];

          $test->assertEquals($want_code, $code);
          $test->assertNotEmpty($body);

          $o = json_decode($body);
          $r[] = $o;
          $test->assertNotNull($o);

          $test->assertEquals("ok", $o->status);
          $test->assertNull($o->error);

          return $r;
      });
  }

  function api_error(Attempt $req_response, $test, $want_code = 500) {
    if ($req_response->isFailure()) {
      $test->assertTrue(false);
      return new Failure();
    }

    return $req_response->map(function($r) use ($test, $want_code) {
      $code = $r[0];
      $body = $r[1];

      $test->assertEquals($want_code, $code);
      $test->assertNotEmpty($body);

      $o = json_decode($body);
      $r[] = $o;
      $test->assertNotNull($o);

      $test->assertEquals("error", $o->status);
      $test->assertNotNull($o->error);

      return $r;
    });
  }
?>
