<?php

namespace GPing\DB;

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

?>
