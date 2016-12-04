<?php

/*
 * Store an incoming ping.
 */

require_once('core.php');
load('db/db_write.php');

$resp = Array();
$resp['result'] = "noop";

if ($_REQUEST['ver']) {

    //    if ($_REQUEST['ver']>8) {
    //     ob_start("ob_gzhandler");
    //    }

    $id = $_REQUEST['id'];

    if (strlen($id)<12) {
        $id = generateRandomString(12);
    }

    $resp['id'] = $id;
    $resp['result'] = "ok";
    $r = $_REQUEST;

    if ($_REQUEST['glat']) {
      if ($err = write_gloc($id, $r['glat'], $r['glng'], $r['gaccuracy'])) {
        $resp['error'] = $err;
      }
        $s = "REPLACE INTO gping_gloc (id,t,time,lat,lng,accuracy) values (" .
         "'".mysql_real_escape_string($id)."'," .
         "now()," .
         "from_unixtime(".(($_REQUEST['gtime']*1)/1000).")," .
         "'".mysql_real_escape_string($_REQUEST['glat'])."'," .
         "'".mysql_real_escape_string($_REQUEST['glng'])."'," .
         "'".mysql_real_escape_string($_REQUEST['gaccuracy'])."')";
        echo("exec query 1: $s\n");
        $result = mysql_query($s) or $resp['error'] = mysql_error();
    }


    if ($_REQUEST['nlat']) {
      if ($err = write_nloc($id, $r['ntime'], $r['nlat'], $r['nlng'], $r['naccuracy'])) {
        $resp['error'] = $err;
      }
      $s = "REPLACE INTO gping_nloc (id,t,time,lat,lng,accuracy) values (" .
       "'".mysql_real_escape_string($id)."'," .
       "now()," .
       "from_unixtime(".(($_REQUEST['ntime']*1)/1000).")," .
       "'".mysql_real_escape_string($_REQUEST['nlat'])."'," .
       "'".mysql_real_escape_string($_REQUEST['nlng'])."'," .
       "'".mysql_real_escape_string($_REQUEST['naccuracy'])."')";
        echo("exec query 2: $s\n");
      $result = mysql_query($s) or $resp['error'] = mysql_error();
    }



    $sql = "REPLACE INTO gping (id,ver,bat_percent,bat_charge,bat_status,";
    $dat = "" .
       "'".mysql_real_escape_string($id)."'," .
       "'".mysql_real_escape_string($_REQUEST['ver'])."'," .
       "".mysql_real_escape_string($_REQUEST['bat_percent'])."," .
       "'".mysql_real_escape_string($_REQUEST['bat_charge'])."'," .
       "'".mysql_real_escape_string($_REQUEST['bat_status'])."',";


    if ($_REQUEST['account']) {
        $sql .= "account,";
        $dat .= "'".mysql_real_escape_string($_REQUEST['account'])."',";
    }
    if ($_REQUEST['aid']) {
        $sql .= "aid,";
        $dat .= "'".mysql_real_escape_string($_REQUEST['aid'])."',";
    }
    if ($_REQUEST['locked']) {
        $sql .= "locked,";
        $dat .= "'".mysql_real_escape_string(substr($_REQUEST['locked'],0,1))."',";
    }
    if ($_REQUEST['odbs']) {
        $sql .= "odbs,";
        $dat .= "'".mysql_real_escape_string($_REQUEST['odbs'])."',";
    }
    if ($_REQUEST['fleet_id']) {
        $sql .= "fleetid,";
        $dat .= "'".mysql_real_escape_string($_REQUEST['fleet_id'])."',";
    }
    if ($_REQUEST['obds']) {
        $sql .= "odbs,";
        $dat .= "'".mysql_real_escape_string($_REQUEST['obds'])."',";
    }

    if ($_REQUEST['uptime_phone']) {
        $sql .= "uptime_phone,";
        $dat .= "'".mysql_real_escape_string($_REQUEST['uptime_phone'])."',";
    }

    if ($_REQUEST['uptime_app']) {
        $sql .= "uptime_app,";
        $dat .= "'".mysql_real_escape_string($_REQUEST['uptime_app'])."',";
    }

    if ($_REQUEST['voltage']) {
        $sql .= "voltage,";
        $dat .= "'".mysql_real_escape_string($_REQUEST['voltage'])."',";
    }

    $sql .= "t)";
    $dat .= "now()";

    $s = $sql . " values (" . $dat . ")";

    $result = mysql_query($s) or $resp['error'] = mysql_error();
    echo("exec query 3: $s\n");

}


echo("\n\n");
print (json_encode($resp));
echo("\n\n");
?>
