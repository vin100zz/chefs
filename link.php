<?php

$link = array();
$link["Antigua"] = "Antigua_et_Barbuda";
$link["Saint-Vincent et Gr."] = "Saint-Vincent._et_G";
$link["Sainte-Lucie"] = "Ste-Lucie";
$link["Côte d'Ivoire"] = "Côte_d%27Ivoire";
$link["Sao Tome et Principe"] = "Sao_Tom%e9_et_Principe";
$link["Allemagne de l'est"] = "Allemagne_de_l%27est";

function getLink($pays)
{
  global $link;

  if(isset($link[$pays])) {
    return $link[$pays];
  }
  return str_replace(' ', '_', $pays);
}


?>

