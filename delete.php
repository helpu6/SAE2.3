<?php
session_start(); // Important pour utiliser $_SESSION

echo "Test début"; // Vérifie si le script commence à s'exécuter

// Vérifie si 'IdReservation' et 'prenom' sont dans $_POST et $_SESSION
echo "<pre>";
print_r($_POST); // Affiche le contenu de $_POST
print_r($_SESSION); // Affiche le contenu de $_SESSION
echo "</pre>";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['IdReservation']) && isset($_SESSION['prenom'])) {
    echo "Conditions OK"; // Si la condition du if est respectée

    // Récupération des données
    $id = intval($_POST['IdReservation']); // Assure-toi que l'ID est un entier
    $prenom = $_SESSION['prenom'];

    // Vérification de l'existence de la réservation avant suppression
    $pdo = new PDO("mysql:host=mysql_serv;dbname=wmzoughi_05", "wmzoughi", "GRP14tropfort");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Affiche les erreurs PDO

    $stmt = $pdo->prepare("SELECT * FROM reservation WHERE IdReservation = ? AND Prenom = ?");
    $stmt->execute([$id, $prenom]);

    echo "Exécution de la requête"; // Vérifie si la requête est exécutée

    if ($stmt->rowCount() > 0) {
        echo "Réservation trouvée"; // Si la réservation est trouvée

        // Préparer et exécuter la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM reservation WHERE IdReservation = ? AND Prenom = ?");
        $stmt->execute([$id, $prenom]);

        // Vérifier si la suppression a réussi
        if ($stmt->rowCount() > 0) {
            echo "Suppression réussie"; // Si la suppression a été effectuée
            header("Location: Equipage.php"); // Redirection vers la page Equipage après suppression
            exit();
        } else {
            echo "Erreur lors de la suppression de la réservation.";
        }
    } else {
        echo "Aucune réservation trouvée pour l'utilisateur.";
    }
} else {
    echo "Condition POST ou SESSION non remplie"; // Si la condition n'est pas remplie
}
?>