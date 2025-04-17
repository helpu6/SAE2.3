<?php
session_start();

// Vérifie que le nom et prénom sont présents
if (!isset($_GET['nom']) || !isset($_GET['prenom'])) {
    die("Champs nom et prénom requis !");
}

$nom = $_GET['nom'];
$prenom = $_GET['prenom'];

require_once('db_Class.php');
$database = new DB();
$db = $database->connexion();

try {
    // Requête pour trouver l’étudiant
    $stmt = $db->prepare("SELECT * FROM etudiant WHERE Nom = :nom AND Prenom = :prenom");
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->execute();
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($etudiant) {
        $_SESSION['IdE'] = $etudiant['IdE'];
        $_SESSION['nom'] = $etudiant['Nom'];
        $_SESSION['prenom'] = $etudiant['Prenom'];
        header("Location: Equipage.php");
        exit();
    } else {
        echo "<script>alert('Aucun étudiant trouvé. Veuillez vous inscrire.');</script>";
        echo "<script>window.location.replace('subscribe.html');</script>";
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
