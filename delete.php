<?php

include_once 'dbaccess.php';
include_once 'logger.php';

function getParam($param) {
  if(isset($_REQUEST[$param])) {
    return $_REQUEST[$param];
  }
  return "";
}

$id = $_REQUEST['id'];

Logger::log("==================");
Logger::log("* id=$id");

$query = "DELETE FROM chronologie WHERE Id=$id;";
DBAccess::exec($query);
Logger::log($query);

print $query;

?>