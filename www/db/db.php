<?php
  require_once($_SERVER["DOCUMENT_ROOT"] . '/core.php');
  load('config.php');
  load('util.php');

  $db = get_db();

  function readGPSHistory($id, $limit = 120) {
    global $db;

    if (!isset($limit) || !is_int($limit) || $limit < 0) {
      $limit = 120;
    }

    $esc_id = mysql_real_escape_string($id);
    $id_clause = "id = '${esc_id}'";

    $sql = <<<QUERY
SELECT
  id,
  t,
  lng,
  lat,
  time,
  accuracy as acc
FROM  gping_gloc
WHERE ${id_clause} AND lat > 0
ORDER BY t DESC LIMIT ${limit}
QUERY;

    return new QueryResult($db, mysql_query($sql, $db));
  }

  function test() {
    global $db;
    $r = mysql_query('select t from gping_gloc;', $db);
    $r = readGPSHistory('5sld5lya3p21');

    if ($r->err()) {
      print_r($r->err());
      return;
    }

    echo $r->count() . " rows\n";

    //while ($row = $r->row()) {
    $row = $r->row();
      echo json_encode($row) . "\n";
      $xy .= "{lat: ".$row['lat'].", lng: ".$row['lng']."},";
      echo $xy . "\n";
    //}
  }

  class QueryResult {
    private $error;
    private $result;
    private $num_rows;

    function __construct($db, $result) {
      $this->result = $result;
      $this->error = mysql_error($db);

      if ($result) {
        $this->num_rows = mysql_num_rows($result);
      } else {
        $this->num_rows = 0;
      }
    }

    function count() {
      return $this->num_rows;
    }

    function row() {
      $next = mysql_fetch_assoc($this->result);
      // I don't actually know if mysql_fetch_assoc can set mysql_error... *shrug*
      $this->error = mysql_error();
      return $next;
    }

    function err() {
      return $this->error;
    }
  }
?>
<!--pre>
<?php
  // test();
?>
</pre-->

