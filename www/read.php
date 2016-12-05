<?php

require_once('core.php');
load('util.php');
load('db/db_read.php');

$ver = -1;

$db = get_db();
$lng  = 0;
$lat  = 0;
$demo = 0;
$resp = Array();

$resp['result'] = "noop";

function pinTitle($ll, $type) {
  $time_since = time_elapsed_string($ll->time);
  return "Last ${type}<br>" .
    "<i>${time_since}</i><br><br>" .
    "time=$ll->time<br>" .
    "lat=$ll->lat<br>" .
    "lng=$ll->lng<br>".
    "accuracy=" . number_format($ll->acc,1) . " meters<br>";
}

function odbBanner($info) {
  if (!$info) {
    return "No ODB-II Info";
  }

  return "OBD-II Info (updated ".time_elapsed_string($info['t']).")<br>" .
    "<pre>" . $info['odbs']."</pre>" .
    "<small>As of ".$info['t'].".</small>";
}


// readTracking reads tracking data and returns a constructed pin title, lat/lng
// set, and the first (newest) coordinate reading.
function readTracking($id, $type, $query_fn) {
  $title = "No $type Data";
  $lat_lng_set = new LatLngSet();
  $lat = "51.1787814";
  $lng = "-1.8266395";

  $demo = ($id == get_demo_id());

  $gloc = $query_fn($id);
  $lat_lng_set = new LatLngSet();

  if (!$gloc->err()) {
    while ($row = $gloc->row()) {
      $lat_lng_set->addRow($row);
    }
    if ($demo) { $lat_lng_set->futz(); }
    if ($lat_lng_set->size() > 0) {
      $f = $lat_lng_set->first();
      $title = pinTitle($f, $type);
      $lat = $f->lat;
      $lng = $f->lng;
    }
  }

  return [$title, $lat_lng_set, $lat, $lng];
}

if ($_REQUEST['id']) {
    $title = "No GPS data";

    $id = $_REQUEST['id'];
    $demo = ($id == get_demo_id());

    $gpsTracking = readTracking($id, 'GPS Location', readGPSHistory);
    $title = $gpsTracking[0];
    $glocCoords = $gpsTracking[1];
    $lat = $gpsTracking[2];
    $lng =  $gpsTracking[3];

    $netTracking = readTracking($id, 'Network Location', readNetworkHistory);
    $title2 = $netTracking[0];
    $nlocCoords = $netTracking[1];
    $n_lat = $netTracking[2];
    $n_lng =  $netTracking[3];
}

$odbResult = readLastODB($id);

$odbInfo = false;
if (!$odbResult->err() && $odbResult->count() > 0) {
  $odbInfo = $odbResult->row();
  $odbInfo['odbs'] = str_replace(get_demo_vin(),"1FM000000031337", $odbInfo['odbs']);
}

$vData = readVoltage($id);
$voltage = "unknown";
$vv = "";

if (!$vData->err() && $vData->count() > 0) {
  while ($row = $vData->row()) {
    if ($voltage == "unknown") {
      $ver = $row['ver'];
      $voltage = "Voltage: <code>" . $row['voltage'] . "V</code>  (updated ".time_elapsed_string($row['t']).")";
    }
    $vv = "[" .   $row['voltage'] . "], " . $vv;
  }
  $vv = substr($vv, 0, -2);
} else {
  echo "404 $id not found";
  exit;
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>gping.io</title>
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: Roboto, Arial, sans-serif; font-size: 13px;
      }
      #map {
        height: 100%;
      }
    </style>
    <script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1.1','packages':['imagesparkline']}]}"></script>
  </head>
  <body>
    <div id="map"></div>
    <div style="font-family:sans-serif; bottom:10px;left:10px; position:absolute">
        <div id="chart_div" style="padding:8px;; background:white; border-radius:2px; color: rgb(86, 86, 86); font-family: Roboto, Arial, sans-serif; font-size: 11px;  box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; min-width: 22px; background-color: rgb(255, 255, 255); background-clip: padding-box;"></div>
    </div>
    <div style="font-family:sans-serif; top:10px;right:10px;padding:1em; position:absolute; background:white; border-radius:4px; opacity:.8; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; "><?= odbBanner($odbInfo) ?></div>
    <div style="font-family:sans-serif; top:10px;left:114px;padding:8px;position:absolute; background:white; border-radius:2px; color: rgb(86, 86, 86); font-family: Roboto, Arial, sans-serif; font-size: 11px;  box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; min-width: 22px; background-color: rgb(255, 255, 255); background-clip: padding-box;"><? print $voltage; ?></div>
    <div id="capture"></div>
<script>

<? if ($vv != "") { ?>
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['V'],
            <?= $vv ?>
        ]);
        var chart = new google.visualization.ImageSparkLine(document.getElementById('chart_div'));

        chart.draw(data, {width: 280, height: 29, showAxisLines: false,  showValueLabels: true, labelPosition: 'right'});
    }

    google.setOnLoadCallback(drawChart);

<? } ?>



function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 18,
        center: {lat: <? print $lat; ?>, lng: <? print $lng; ?>},
        mapTypeId: google.maps.MapTypeId.SATELLITE
    });

    var vPlanCoordinates = <?= $glocCoords->mapPath(); ?>;

    var vPath = new google.maps.Polyline({
        path: vPlanCoordinates,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });

    var vPlanCoordinates2 = <?= $nlocCoords->mapPath(); ?>;

    var vPath2 = new google.maps.Polyline({
        path: vPlanCoordinates2,
        geodesic: true,
        strokeColor: '#FFFF00',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });

    var infowindow = new google.maps.InfoWindow({
        content: '<? print $title; ?>'
    });

    var infowindow2 = new google.maps.InfoWindow({
        content: '<? print $title2; ?>'
    });

    var marker = new google.maps.Marker({
        position: {lat: <? print $lat; ?>, lng: <? print $lng; ?>},
        map: map,
        title: 'Last Network Location Ping'
    });

    var marker2 = new google.maps.Marker({
        position: {lat: <? print $n_lat; ?>, lng: <? print $n_lng; ?>},
        map: map,
        title: 'Last GPS Ping'
    });


    vPath.setMap(map);
    vPath2.setMap(map);
    infowindow.open(map, marker);


}
</script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<? echo get_gmap_key(); ?>&callback=initMap">
    </script>
  </body>
</html>
