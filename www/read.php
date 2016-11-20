<?php

require_once('core.php');
load('util.php');
load('db/db.php');

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


if ($_REQUEST['id']) {
    $title = "No GPS data";

    $id = $_REQUEST['id'];
    $demo = ($id == get_demo_id());

    $gloc = readGPSHistory($id);
    $glocCoords = new LatLngSet();

    if (!$gloc->err()) {
      while ($row = $gloc->row()) {
        $glocCoords->addRow($row);
      }
      if ($demo) { $glocCoords->futz(); }
      if ($glocCoords->size() > 0) {
        $f = $glocCoords->first();
        $title = pinTitle($f, "GPS Location");
        $lat = $f->lat;
        $lng = $f->lng;
      }
    } else {
        $lat = "51.1787814";
        $lng = "-1.8266395";
    }

    $ids=" (id='".mysql_real_escape_string($id)."') ";
    $sql = "select * from gping_nloc where $ids and lat >0 order by t desc limit 50";

    $result = mysql_query($sql) or $resp['error'] = mysql_error();

    $xy2 = "";
    $title2 = "";

    if ($result && mysql_num_rows($result)>0) {

        while ($row = mysql_fetch_assoc($result)) {
            if ($demo==1) {
                $row['lat'] += sin($row['hour'])/100 - .05;
                $row['lng'] -= abs(cos($row['hour'])/50) - .1;
            }
            $xy2 .= "{lat: ".$row['lat'].", lng: ".$row['lng']."},";
            if (!$lat2) {
             $lat2 = $row['lat'];
             $ver2 = $row['ver'];
             $lng2 = $row['lng'];
             $title2 = "Last Network Location<br><i>".time_elapsed_string($row['time'])."</i><br><br>time=".$row['time']."<br>lat=$lat<br>lng=$lng<br>accuracy=". number_format ($row['accuracy'],1)." meters<br>";
            }
        }
    }

    if (!$lat2) {
        $lat2 = $lat;
        $lng2 = $lng;
    }


}

  $voltage = "";

  $sql = "select t,odbs from gping where $ids and odbs is not null order by gping.t desc limit 1";

  $result = mysql_query($sql);

  $info = "OBD-II information N/A";

  if ($result && mysql_num_rows($result)>0) {
      if ($row = mysql_fetch_assoc($result)) {
         $o = $row['odbs'];
         $o = str_replace(get_demo_vin(),"1FM000000031337",$o);
         $info = "OBD-II Info (updated ".time_elapsed_string($row['t']).")<br><pre>" . $o."</pre><small>As of ".$row[t].".</small>";
      }
  }

  $sql = "select ver,voltage,t from gping where $ids order by gping.t desc limit 120";

  $result = mysql_query($sql);

  $voltage = "unknown";
  $vv = "";

  if ($result && mysql_num_rows($result)>0) {
      while ($row = mysql_fetch_assoc($result)) {

         if ($voltage=="unknown") {
             $ver = $row['ver'];
             $voltage = "Voltage: <code>" . $row['voltage'] . "V</code>  (updated ".time_elapsed_string($row['t']).")";
         }
        $vv = "[" .   $row['voltage'] . "]," . $vv;

      }
  } else {
        echo "404 You Suck at Typing";
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
    <div style="font-family:sans-serif; top:10px;right:10px;padding:1em; position:absolute; background:white; border-radius:4px; opacity:.8; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; "><? print $info; ?></div>
    <div style="font-family:sans-serif; top:10px;left:114px;padding:8px;position:absolute; background:white; border-radius:2px; color: rgb(86, 86, 86); font-family: Roboto, Arial, sans-serif; font-size: 11px;  box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; min-width: 22px; background-color: rgb(255, 255, 255); background-clip: padding-box;"><? print $voltage; ?></div>
    <div id="capture"></div>
<script>

<? if ($vv) { ?>
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['V'],
            <? print $vv; ?>
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

    var vPlanCoordinates2 = [ <? print $xy2; ?> ];

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
        position: {lat: <? print $lat2; ?>, lng: <? print $lng2; ?>},
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
