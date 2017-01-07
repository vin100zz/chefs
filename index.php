<?php

include_once 'html.php';
include_once 'dbaccess.php';
include_once 'combi_pays.php';
include_once 'flags.php';

global $combi_pays;
global $country_codes;

function drawMultipleSelect($data, $id, $text, $multiple)
{
    println("<label for='$id'>$text :</label>");
    println("<select " . ($multiple ? "multiple" : "") . " id='$id' name='$id' data-placeholder='Tous'>");
    println("<option value=''></option>");
    foreach($data as $value)
    {
      $sqlValue = str_replace("'", "&#39;", $value);
      println("<option value='$sqlValue'>$value</option>");
    }
    println("</select>");
}

beginHtml();

// filtres
$pays = DBAccess::singleColumn("SELECT DISTINCT(pays) FROM chronologie ORDER BY pays");
$evt = DBAccess::singleColumn("SELECT DISTINCT(evenement) FROM chronologie ORDER BY evenement");
$perso = DBAccess::singleColumn("SELECT DISTINCT(nouveau) FROM chronologie ORDER BY nouveau");
$titre = DBAccess::singleColumn("SELECT DISTINCT(titre) FROM chronologie ORDER BY titre");

println("<div id='dialog_filtres' class='dialog' title='Filtres'>");
	println("<form id='filtres'>");
	
		println("<label for='date' id='filtre_label_date'>Date :</label>");
		println("<input type='text' id='filtre_date' class='input_slider' readonly='readonly' />");
		println("<div id='filtre_date_slider'></div>");
		
		drawMultipleSelect($pays, "filtre_pays", "État", true);
		drawMultipleSelect(array_keys($combi_pays), "filtre_combipays", "Super État", true);
		drawMultipleSelect($evt, "filtre_evt", "Événement", true);
		drawMultipleSelect($perso, "filtre_perso", "Chef", true);
		drawMultipleSelect($titre, "filtre_titre", "Titre", true);
	
		println("<label>En Cours ?</label>");
		println("<input type='checkbox' id='filtre_encours' /><label for='filtre_encours' id='filtre_label_encours'>&#10004;</label>");

		println("<button id='button_filtres_ok'>Filtrer</button>");
    println("<button id='button_filtres_reset'>Réinitialiser</button>");
	println("</form>");
println("</div>");

// quick filtre
println("<div id='quick_filtre'>");
println("<div class='title'>Filtrer par pays</div>");
println("<ul>");
  $letter = '';
  foreach($pays as $item)
  {
    //$item = str_replace("'", "&aquot;", $item);
    $newLetter = substr($item, 0, 1);
    if ($newLetter != $letter) {
      $letter = $newLetter;
      println("<li class='letter'><span>$letter</span></li>");
    }
    $code = (isset($country_codes[$item]) ? $country_codes[$item] : "xx");
    println("<li><div class='flag flag32 $code' title=\"$item\"></div></li>");
  }
println("</ul>");
println("</div>");

// timeline
println("<div id='timeline'></div>");

// table
println("<table id='table_chronologie' width='100%'>");
println("<thead><td>Id</td><td width='130px'>Date</td><td width='170px'>Pays</td><td width='120px'>Événement</td><td>Ancien</td><td>Nouveau</td><td width='120px'>Titre</td><td width='120px'>Départ</td><td width='120px'>Durée</td><td width='50px'></td></thead>");
println("<tbody>");

println("</tbody>");
println("</table>");
println("<div id='table_chronologie_bottom'></div>");

// new/edit
println("<form id='dialog_newedit' class='dialog' title='Saisie événement'>");
  println("<input type='hidden' id='newedit_id' name='newedit_id'>");
  
  println("<label for='newedit_date'>Date :</label>");
  println("<input type='date' id='newedit_date' name='newedit_date' placeholder='Date'>");

  drawMultipleSelect($pays, "newedit_pays", "État", false);
  drawMultipleSelect($evt, "newedit_evt", "Événement", false);
  //drawMultipleSelect($perso, "newedit_ancien", "Ancien", false);

  println("<label for='newedit_nouveau'>Nouveau :</label>");
  println("<input type='text' id='newedit_nouveau' name='newedit_nouveau' placeholder='Nouveau'>");

  drawMultipleSelect($titre, "newedit_titre", "Titre", false);

  /*println("<label for='newedit_depart'>Départ :</label>");
  println("<input type='date' id='newedit_depart' name='newedit_depart' placeholder='Départ'>");*/

  println("<button id='button_newedit_ok'>Valider</button>");
  println("<button id='button_newedit_cancel'>Annuler</button>");
  
println("</form>");

// delete
println("<div id='dialog_delete' class='dialog' title='Mèffi !'>");
	println("<span>Êtes-vous sur de vouloir supprimer l'événement ?</span>");
	println("<button id='button_delete_yes'>Oui</button>");
	println("<button id='button_delete_no'>Non</button>");
println("</div>");

endHtml();

?>