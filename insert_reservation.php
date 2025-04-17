<?php
$host = "mysql_serv";
$db   = "wmzoughi_05";
$user = "wmzoughi";
$pass = "GRP14tropfort";
$conn = new mysqli($host, $user, $pass, $db);

$ide = 2; // Id de l'utilisateur passager
$idCovoiturage = $_POST['id_covoiturage'];
$dateReservation = date("Y-m-d");

// Vérifie s'il reste des places
$stmt = $conn->prepare("SELECT PlacesDispo FROM covoiturage WHERE IdCovoiturage = ?");
$stmt->bind_param("i", $idCovoiturage);
$stmt->execute();
$stmt->bind_result($places);
$stmt->fetch();
$stmt->close();

if ($places <= 0) {
    echo "<p style='color:red;'>😢 Plus de places disponibles !</p>";
    exit();
}

// Enregistre la réservation
$stmt = $conn->prepare("INSERT INTO reservation (IdE, IdCovoiturage, DateReservation, Statut) VALUES (?, ?, ?, 'En attente')");
$stmt->bind_param("iis", $ide, $idCovoiturage, $dateReservation);
$stmt->execute();

// Met à jour les places
$conn->query("UPDATE covoiturage SET PlacesDispo = PlacesDispo - 1 WHERE IdCovoiturage = $idCovoiturage");

echo "<p style='color:green;'>✅ Réservation confirmée !</p><a href='reserver.php'>← Retour</a>";
