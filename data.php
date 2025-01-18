<?php

include_once 'dbaccess.php';
include_once 'flags.php';
include_once 'combi_pays.php';
include_once 'link.php';

function escape($str) {
  return str_replace("'", "''", $str);
}

function setWhereClause($param, $col)
{
	if(isset($_REQUEST[$param]))
	{
		$value = escape($_REQUEST[$param]);
		$values = explode(",", $value);
		$values = "'" . implode("', '", $values) . "'";
		return "$col IN(" . $values . ")";
	}
}


function formatDate($strDate)
{
	if($strDate === "")
		return "";
	
	$nom_mois = array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");


	$date = strtotime($strDate);
	$jour = date('d', $date); $jour = (substr($jour, 0, 1) === "0" ? substr($jour, 1, 1) : $jour);
	$mois = $nom_mois[date('m', $date)-1];
	$annee = date('Y', $date);
		
	return "<span hidden='hidden'>$strDate</span>" . $jour . ($jour === "1" ? "er" : "") . " " . $mois . " " . $annee;
}


function formatDuree($row)
{
	// debut
	$debut = strtotime($row["Date"]);

	$isFutureEvent = false;
	
	// fin
	if($row["Depart"] === "")
	{
		if($row["Nouveau"] === "")
			return "";
			
		$fin = time();
		$isFutureEvent = $fin < $debut;
	}
	else
		$fin = strtotime($row["Depart"]);
	
	// nb jours
	$nbJoursTot = floor( abs($fin-$debut) / (60*60*24) );

	if ($isFutureEvent) {
		++$nbJoursTot;
	}

	$nbAnnees = floor($nbJoursTot/365);
	$nbMois = floor( ($nbJoursTot%365) / 30 );
	$nbJours = floor( ($nbJoursTot%30) );
	 
	// annees
	if($nbAnnees >= 1)
		$str = $nbAnnees . " an" . (abs($nbAnnees) > 1 ? "s" : "") . ($nbMois > 1 ? " $nbMois mois" : "");
	else if($nbMois >= 1)
		$str = $nbMois . " mois" . (abs($nbJours) > 1 ? " $nbJours jour" . ($nbJours > 1 ? "s" : "") : "");
	else
		$str = "$nbJours jour" . (abs($nbJours) > 1 ? "s" : "");

	if ($isFutureEvent) {
		$str = "dans " . $str;
	}
	
	return "<span hidden='hidden'>" . str_pad($nbJoursTot, 5, "0", STR_PAD_LEFT) . "</span>$str";
}


function formatPays($pays)
{
	global $country_codes;
	$code = (isset($country_codes[$pays]) ? $country_codes[$pays] : "xx");
	return "<span hidden='hidden'>$pays</span><div class='flag flag16 $code'></div><a href='etats/index.php/" . getLink($pays) . "'>$pays</a>";
}


// read parameters and build SQL query
$params = array();


$pays = setWhereClause("pays", "pays");
if($pays != "") $params[] = $pays;


$evt = setWhereClause("evt", "evenement");
if($evt != "") $params[] = $evt;


$ancien = setWhereClause("perso", "ancien");
$nouveau = setWhereClause("perso", "nouveau");
if($ancien != "") { $perso = "(" . $ancien . " OR " . $nouveau . ")"; $params[] = $perso; }


$titre = setWhereClause("titre", "titre");
if($titre != "") $params[] = $titre;


if(isset($_REQUEST["date"]) && $_REQUEST["date"] != "")
{
	$date = explode("-", $_REQUEST["date"]);
	$params[] = "(Date >= '" . $date[0] . "-01-01' AND Date <= '" . $date[1] . "-12-31')";
}


if(isset($_REQUEST["enCours"]))
{
	$enCours = $_REQUEST["enCours"];
	if($enCours === "true")
		$params[] = "Depart = '' AND Nouveau <> ''";
}


if(isset($_REQUEST["combiPays"]))
{
	$values = explode(",", $_REQUEST["combiPays"]);
	
	global $combi_pays;
	
	$listePays = array();	
	foreach($values as $value)
	{
		$paysForValue = array_values($combi_pays[$value]);
		foreach($paysForValue as $pays)
		{
			$listePays[] = escape($pays);
		}
	}
	
	$values = "'" . implode("', '", $listePays) . "'";
	$params[] = "Pays IN(" . $values . ")";
}


$where = implode(" AND ", $params);
if($where != "") $where = " WHERE " . $where;


// DB
$query = "SELECT * FROM chronologie " . $where;
$chefs = DBAccess::query($query);


// result
$json_rows = array();


for($i=0; $i<count($chefs); ++$i)
{
	$row = $chefs[$i];
	
	$json_row = array();
	
  $json_row[] = $row["Id"];
	$json_row[] = formatDate($row["Date"]);
	$json_row[] = formatPays($row["Pays"]);
	$json_row[] = $row["Evenement"];
	$json_row[] = $row["Ancien"];
	$json_row[] = $row["Nouveau"];
	$json_row[] = $row["Titre"];
	$json_row[] = formatDate($row["Depart"]);
	$json_row[] = formatDuree($row);
	
	$json_row[] = "<a class='action edit'></a><a class='action delete'></a>";
  
  $raw = array();
  foreach($row as $key => $val) {
    $raw[$key] = $val;
  }
  $json_row[] = $raw;
	
	$json_rows[] = $json_row;
}


$json = array();
$json["aaData"] = $json_rows;


print json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);


?>