<?php
$pdo = new PDO("mysql:host=mysql_serv;dbname=wmzoughi_05", "wmzoughi", "GRP14tropfort");

$donnees = [];

$resultat = $pdo->query("SELECT Domicile FROM etudiant");

$données = [];

foreach ($resultat as $row) {
    $données[] = $row['Domicile'];
}

echo json_encode($données);
?>  