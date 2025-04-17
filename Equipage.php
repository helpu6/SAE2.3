<?php
// === Inclusion de la classe de connexion à la base de données ===
$dbFile = 'db_class.php';
if (!file_exists($dbFile)) {
    die("Erreur : Le fichier '$dbFile' est introuvable.");
}
require_once($dbFile);

// Connexion à la base de données
$database = new DB();
$db = $database->connexion();

// === Inclusion de la classe Equipage ===
$equipageFile = 'Equipage.Class.php';
if (!file_exists($equipageFile)) {
    die("Erreur : Le fichier '$equipageFile' est introuvable.");
}
require_once($equipageFile);

// Instanciation de la classe Equipage
$requests = new Equipage($db);

// Récupération des équipages
$array = $requests->setEquipages();

$prenom = $_SESSION['prenom'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Equipage Page</title>
    <link rel="stylesheet" href="equipage.css">
</head>

<body>
    <nav id="nav_log">
        <div id="nom_accueil">
            <a href="index.html">IUT RIDE</a>
        </div>
    </nav>
    <main>
        <section id="result">
            <h2>Vos réservation</h2>
            <hr>
            <?php
            // Vérifie que l'utilisateur est connecté
            if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
                die("Erreur : utilisateur non connecté.");
            }

            // Requête pour récupérer les réservations de l'utilisateur
            $req = "
            SELECT r.*, c.LieuDepart, c.LieuArrivee, c.DateTrajet, c.HeureDepart,IdReservation
            FROM reservation r
            JOIN covoiturage c ON r.IdCovoiturage = c.IdCovoiturage
            WHERE r.Prenom = '$prenom'";
            // Exécution de la requête
            $result = $db->query($req);

            // Vérifie si des résultats sont renvoyés
            if ($result) {
                // Récupère le nombre de lignes retournées (si nécessaire)
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);

                if (count($rows) > 0) {
                    // Affiche les résultats
                    foreach ($rows as $row) {
                        echo "<form action='delete.php' method='POST'>";
                        echo "<input type='hidden' name='IdReservation' value='" . htmlspecialchars($row['IdReservation']) . "'>"; // ID de la réservation
                        echo "<button type='submit' style='background-color:red;color:white;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;'>
                            Supprimer la réservation
                        </button>";
                        echo "</form>";
        
                        echo "<div class='reservation'>";
                        echo "<p><strong>IdReservation</strong> " . htmlspecialchars($row['IdReservation']) . "</p>";
                        echo "<p><strong>Date:</strong> " . htmlspecialchars($row['DateReservation']) . "</p>";
                        echo "<p><strong>Date de trajet:</strong> " . htmlspecialchars($row['DateTrajet']) . "</p>";
                        echo "<p><strong>Lieu de Depart</strong> " . htmlspecialchars($row['LieuDepart']) . "</p>";
                        echo "<p><strong>Lieu d'arrivée</strong> " . htmlspecialchars($row['LieuArrivee']) . "</p>";
                        echo "<p><strong>Heure de Depart</strong> " . htmlspecialchars($row['HeureDepart']) . "</p>";
                        echo "</div>";
                    }
                }
            }
            ?>
            <li><a href="reserve.php">Réserver votre billet</a></li>
        </section>
    </main>

    <footer>
        <div id="copyrightEtIcons">
            <div id="logo_fin">
                <img src="logo_Cov_Auto.png" alt="Logo de l'application">
            </div>
            <div id="copyright">
                <span>by MZOUGHI Wassim, MVULA Sven et AISSA Anis</span>
            </div>
        </div>
    </footer>
</body>

</html>