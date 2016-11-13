<?php
  require_once($_SERVER["DOCUMENT_ROOT"] . '/core.php');
  load('config.php');

  function readGPSHistory($id, $limit = 120) {
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
  concat(
    concat(
      mid(t,12,2),
      '.',
      concat(mid(t,15,2))
    )
  ) as hour
FROM  gping_gloc
WHERE ${id_clause} AND lat > 0
ORDER BY t DESC LIMIT ${limit}
QUERY;

    $result = mysql_query($sql);
    if (!$result) {
      $resp['error'] = mysql_error();
    }
  }
?>
