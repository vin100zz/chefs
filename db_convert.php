<?php


include_once 'dbaccess.php';


set_time_limit(60000); // 10 minutes


function convertDate($csvDate) // "2/4/1945 00:00:00" -> "1945-04-02"
{
	if($csvDate == "") return "";


	$date = explode(" " , $csvDate);
	$date = $date[0];
	$date = explode("/" , $date);
	
	$annee = $date[2];
	$mois = $date[1]; $mois = (strlen($mois) > 1 ? $mois : "0" . $mois);
	$jour = $date[0]; $jour = (strlen($jour) > 1 ? $jour : "0" . $jour);
	
	return $annee . "-" . $mois . "-" . $jour;
}


function convertDuree($csvDuree)
{
	$array = explode("," , $csvDuree);
	return $array[0];
}


DBAccess::exec("DELETE FROM chronologie");

if(($handle = fopen("chronologie.csv", "r")) !== FALSE)
{
    while(($data = fgetcsv($handle, 9999, ";")) !== FALSE)
	{
		$values = "'" . convertDate($data[0]) . "'";
        for($i=1; $i < count($data); ++$i)
		{
			if($i != 7)
			{
				$value = $data[$i];
				
				// format conversions
				if($i == 9 || $i == 11)
				{
					$value = convertDate($value);
				}
				else if($i == 6)
				{
					$value = convertDuree($value);
				}
			
				$values .= ", '" . str_replace("'", "''", $value) . "'";
			}
        }
		$query = "INSERT INTO chronologie (date, pays, evenement, ancien, nouveau, titre, duree, finom, findate, vivant, depart) VALUES ($values);";
		
		echo $query;
		echo "<br />";
    
		DBAccess::exec($query);
		
    }
    fclose($handle);
}
?>