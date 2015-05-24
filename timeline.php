<?php

include_once 'dbaccess.php';

// param
$pays = $_GET['pays'];

// DB
$query = "SELECT * FROM chronologie WHERE pays='$pays' ORDER BY date ASC";
$chefs = DBAccess::query($query);

print json_encode($chefs, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);


?>