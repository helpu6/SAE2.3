<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ajout Utilisateur</title>
</head>
<body>

<?php
// Récupération des données du formulaire avec valeurs par défaut
$name = $_POST['name'] ?? '';
$firstname = $_POST['firstname'] ?? '';
$domicile = $_POST['domicile'] ?? '';
$IUT = $_POST['nom_etablissement'] ?? '';
$formation = $_POST['formation'] ?? '';
$groupe = $_POST['groupe'] ?? '';
$sous_groupe = $_POST['sous_groupe'] ?? '';
$immatriculation = $_POST['immatriculation'] ?? '';
$type = $_POST['type'] ?? '';
$marque = $_POST['marque'] ?? '';
$modele = $_POST['modele'] ?? '';
$cg = $_POST['cg'] ?? '';
$ct = $_POST['ct'] ?? '';
$assurance = $_POST['assurance'] ?? '';
$permis = $_POST['permis'] ?? '';
$point_permis = is_numeric($_POST['point_permis'] ?? '') ? (int)$_POST['point_permis'] : 0;
$places = is_numeric($_POST['place'] ?? '') ? (int)$_POST['place'] : 0;

// Inclusion sécurisée des fichiers nécessaires
$basePath = __DIR__;
$dbClassPath = $basePath . '/db_class.php';
$addUserClassPath = $basePath . '/AddUser.Class.php';

if (!file_exists($dbClassPath)) {
    die("Erreur : Fichier db_class.php introuvable.");
}
if (!file_exists($addUserClassPath)) {
    die("Erreur : Fichier AddUser.Class.php introuvable.");
}

require_once($dbClassPath);
require_once($addUserClassPath);

// Connexion à la base
try {
    $database = new DB();
    $db = $database->connexion();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base : " . $e->getMessage());
}

// Création de l'objet AddUser
$requests = new AddUser($db);

// Vérifie si l'étudiant existe déjà
$exist = $requests->existByName($name, $firstname);

if ($exist) {
    echo '<script>alert("Vous êtes déjà enregistré, identifiez-vous."); window.location.replace("connect.html");</script>';
    exit;
} else {
    try {
        $requests->addStudent(
            $name, $firstname, $domicile, $IUT, $formation, $groupe, $sous_groupe,
            $immatriculation, $type, $marque, $modele, $cg, $ct, $assurance,
            $permis, $point_permis, $places
        );
        echo '<script>alert("Ajout effectué avec succès !"); window.location.replace("connect.html");</script>';
        exit;
    } catch (Exception $e) {
        echo '<p style="color:red;">Erreur lors de l\'ajout : ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}
?>

</body>
</html>
