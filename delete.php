<?php

include_once 'dbaccess.php';

function getParam($param) {
  if(isset($_REQUEST[$param])) {
    return $_REQUEST[$param];
  }
  return "";
}

$id = $_REQUEST['id'];

$query = "DELETE FROM chronologie WHERE Id=$id;";
DBAccess::exec($query);

print $query;

?>