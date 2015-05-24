<?php

include_once 'dbaccess.php';
include_once 'logger.php';

function getParam($param) {
  if(isset($_REQUEST[$param])) {
    return str_replace("'", "''", $_REQUEST[$param]);
  }
  return "";
}

$id = getParam('newedit_id');
$date = getParam('newedit_date');
$pays = getParam('newedit_pays');
$evt = getParam('newedit_evt');
$nouveau = getParam('newedit_nouveau');
$titre = getParam('newedit_titre');


// update or insert
if($id != "")
{
  $query = "UPDATE chronologie SET date='$date', pays='$pays', evenement='$evt', nouveau='$nouveau', titre='$titre' WHERE id=$id;";
}
else
{
  $values = "'$date', '$pays', '$evt','$nouveau', '$titre'";
  $query = "INSERT INTO chronologie (date, pays, evenement, nouveau, titre) VALUES ($values);";
}
DBAccess::exec($query);
Logger::log($query);

if ($pays != null && $pays != "") {
  $chefs = DBAccess::query("SELECT * FROM chronologie WHERE pays='$pays' ORDER BY date ASC");
  
  // update anciens
  for ($i=1; $i<count($chefs); ++$i) {
    $row = $chefs[$i];
    $id = $row["Id"];
    $ancien = '';
    if ($row["Date"] != null && $row["Date"] != "") {
      $ancien = $chefs[$i-1]["Nouveau"];
    }
    DBAccess::exec("UPDATE chronologie SET ancien='$ancien' WHERE id='$id'");
    Logger::log("UPDATE chronologie SET ancien='$ancien' WHERE id='$id'");
  }
    
  // update depart
  for ($i=count($chefs)-2; $i>=0; --$i) { 
    $row = $chefs[$i];
    $id = $row["Id"];
    $depart = '';
    if ($row["Date"] != null && $row["Date"] != "") {
      $depart = $chefs[$i+1]["Date"];
    }
    DBAccess::exec("UPDATE chronologie SET Depart='$depart' WHERE id='$id'");
    Logger::log("UPDATE chronologie SET Depart='$depart' WHERE id='$id'");
  }  
}

// response
$perso = DBAccess::singleColumn("SELECT DISTINCT(nouveau) FROM chronologie");

$res = array();
for ($i=0; $i<count($perso); ++$i) {
  $res[] = $perso[$i];
}

print json_encode($res, JSON_PRETTY_PRINT);

?>