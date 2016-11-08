<?

function get_admindb() {
  $link = mysql_pconnect('mysql.dustball.com', 'dustball', 'sqlsqlsql');
  if (!$link) {
    die('Could not connect #200: ' . mysql_error());
  }
  $db_selected = mysql_select_db('dustball', $link);
  if (!$db_selected) {
    die('Can\'t use db #200: ' . mysql_error());
  }
  return $link;
}

function generateRandomString($length = 10) {
    $characters = '23456789abcdefghjkmnpqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function get_db() {
  $link = mysql_pconnect('mysql.dustball.com', 'tpc2010', 'sqlsqlsql');
  if (!$link) {
    die('Could not connect #200: ' . mysql_error());
  }
  $db_selected = mysql_select_db('dustball', $link);
  if (!$db_selected) {
    die('Can\'t use db #200: ' . mysql_error());
  }
  return $link;
}

function get_dojodb() {
  $link = mysql_pconnect('mysql.dustball.com', 'dojosqldb', '1000members');
  if (!$link) {
    die('Could not connect #200: ' . mysql_error());
  }
  $db_selected = mysql_select_db('dojosqldb', $link);
  if (!$db_selected) {
    die('Can\'t use db #200: ' . mysql_error());
  }
  return $link;
}


//mysql> create table gping (lng DECIMAL(11, 8), lat DECIMAL(11, 8), accuracy float, provider char(15), id char(12), t datetime);
//mysql> create index gpi on gping (id, t desc);


// mysql> select id,account,aid,locked,t from gping where ver=15 and id!='zey2ze8jkeqd' and id!='dap6wfd8f67k' and t>'2016-11-07' group by account,id order by t desc limit 100;


$db = get_db();

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
    
    
    

    if ($_REQUEST['glat']) {
        $s = "REPLACE INTO gping_gloc (id,t,time,lat,lng,accuracy) values (" .
            "'".mysql_real_escape_string($id)."'," .
            "now()," .
                "from_unixtime(".(($_REQUEST['gtime']*1)/1000).")," .
                "'".mysql_real_escape_string($_REQUEST['glat'])."'," .
                "'".mysql_real_escape_string($_REQUEST['glng'])."'," .
                "'".mysql_real_escape_string($_REQUEST['gaccuracy'])."')";
        $result = mysql_query($s) or $resp['error'] = mysql_error();
    }
    
    
        if ($_REQUEST['nlat']) {
        $s = "REPLACE INTO gping_nloc (id,t,time,lat,lng,accuracy) values (" .
            "'".mysql_real_escape_string($id)."'," .
            "now()," .
                "from_unixtime(".(($_REQUEST['ntime']*1)/1000).")," .
                "'".mysql_real_escape_string($_REQUEST['nlat'])."'," .
                "'".mysql_real_escape_string($_REQUEST['nlng'])."'," .
                "'".mysql_real_escape_string($_REQUEST['naccuracy'])."')";
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
    

    # $resp['sql'] = $s;    
    $result = mysql_query($s) or $resp['error'] = mysql_error();
    
}


print (json_encode($resp));

      
?>