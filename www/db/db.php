<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/core.php');
load('config.php');
load('util.php');

$db = get_db();

function _limit($limit) {
  if (!isset($limit) || !is_int($limit) || $limit < 0) {
    $limit = 60;
  }

  return $limit;
}

function _idClause($id) {
  $esc_id = mysql_real_escape_string($id);
  return "(id = '$esc_id')";
}

function _readTrackingTable($id, $table, $limit = 120) {
  global $db;

  $esc_id = mysql_real_escape_string($id);
  $id_clause = "id = '${esc_id}'";
  $limit = _limit($limit);

  $sql = <<<QUERY
SELECT
  id,
  t,
  lng,
  lat,
  time,
  accuracy
FROM  ${table}
WHERE ${id_clause} AND lat > 0
ORDER BY t DESC LIMIT ${limit}
QUERY;

    return new QueryResult($db, mysql_query($sql, $db));
  }

function readGPSHistory($id, $limit = 120) {
  return _readTrackingTable($id, "gping_gloc", $limit);
}

function readNetworkHistory($id, $limit = 50) {
  return _readTrackingTable($id, "gping_nloc", $limit);
}

function readLastODB($id) {
  global $db;
  $id_clause = _idClause($id);

  $sql = <<<QUERY
SELECT
  t,
  odbs
FROM
  gping
WHERE
  $id_clause AND
  odbs IS NOT NULL
ORDER BY gping.t desc
LIMIT 1
QUERY;

  return new QueryResult($db, mysql_query($sql, $db));
}

function readVoltage($id, $limit = 120) {
  global $db;
  $id = _idClause($id);
  $limit = _limit($limit);

  $sql = <<<QUERY
SELECT
  ver,
  voltage,
  t
FROM gping
WHERE $id_clause
ORDER BY gping.t DESC
LIMIT ${limit}
QUERY;

  return new QueryResult($db, mysql_query($sql, $db));
}

// QueryResult is a trivial container for interacting with DB results.
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

// LatLng stores a coordinate with timestamp and accuracy.
class LatLng {
  public $lat;
  public $lng;
  public $time;
  public $acc;

  function __construct($lat, $lng, $acc, $ts) {
    $this->lat = $lat;
    $this->lng = $lng;
    $this->acc = $acc;
    $this->time = $ts;
  }
}

// LatLngSet stores a set of LatLng objects and can produce an array in format
// suitable for passing to the Google Maps API. New LatLngs are added to the
// end of the set and order is preserved.
class LatLngSet {
  private $coords;

  function __construct() {
    $this->coords = [];
  }

  function add($lat, $lng, $acc, $time) {
    array_push($this->coords, new LatLng($lat, $lng, $acc, $time));
  }

  function addRow($r) {
    $this->add($r['lat'], $r['lng'], $r['accuracy'], $r['time']);
  }

  function first() {
    if (count($this->coords) < 1) {
      return false;
    }

    return $this->coords[0];
  }

  function last() {
    $cnt = count($this->coords);
    if ($cnt == 0) {
      return false;
    }

    return $this->coords[$cnt-1];
  }

  function size() {
    return count($this->coords);
  }

  function futz() {
    $nudge = function($ll) {
      $n = hourminute($ll->time);
      $ll->lat += sin($n)/100 - .05;
      $ll->lng -= abs(cos($n)/50) - .1;
      return $ll;
    };
    $this->coords = array_map($nudge, $this->coords);
  }

  function mapPath() {
    if ($this->size() == 0) {
      return "[]";
    }

    $print_coord = function($c) {
      return "{\"lat\": $c->lat, \"lng\": $c->lng}";
    };

    return '['.join(', ', array_map($print_coord, $this->coords)).']';
  }
}
?>
