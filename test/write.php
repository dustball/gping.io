<?php
require('_env.php');

function req($id, $ver, $args = []) {
  if ($id != null) { $args['id'] = $id; }
  if ($ver != null) { $args['ver'] = $ver; }

  $arg_str = http_build_query($args);
  $result = `curl -s 'localhost:80/write.php?$arg_str'`;
  return json_decode($result, true);
}
?>
