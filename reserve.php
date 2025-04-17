<?php
session_start();

// Connexion à la base de données
$host = "mysql_serv";
$db   = "wmzoughi_05";
$user = "wmzoughi";
$pass = "GRP14tropfort";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Récupération des données de session
$prenom = $_SESSION['prenom'] ?? '';
$nom = $_SESSION['nom'] ?? '';

// --- Filtrage des covoiturages ---
$filtre_depart = $_GET['depart'] ?? '';
$filtre_arrivee = $_GET['arrivee'] ?? '';

if (!empty($filtre_depart) && !empty($filtre_arrivee)) {
    $stmt = $conn->prepare("SELECT * FROM covoiturage WHERE LieuDepart LIKE ? AND LieuArrivee LIKE ? AND PlacesDispo > 0 ORDER BY DateTrajet ASC");
    $like_depart = "%$filtre_depart%";
    $like_arrivee = "%$filtre_arrivee%";
    $stmt->bind_param("ss", $like_depart, $like_arrivee);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conn->query("SELECT * FROM covoiturage WHERE PlacesDispo > 0 ORDER BY DateTrajet ASC");
}

// --- Publication d'un covoiturage ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publier'])) {
    $ide = 1; // Id du conducteur à rendre dynamique
    $date = $_POST['date_trajet'];
    $heure = $_POST['heure_depart'];
    $depart = $_POST['lieu_depart'];
    $arrivee = $_POST['lieu_arrivee'];
    $places = $_POST['places'];
    $immatriculation = $_POST['immatriculation'];

    $verif = $conn->prepare("SELECT * FROM vehicule WHERE Immatriculation = ?");
    $verif->bind_param("s", $immatriculation);
    $verif->execute();
    $resVerif = $verif->get_result();

    if ($resVerif->num_rows == 0) {
        echo "<p class='message error'>❌ Erreur : ce véhicule n'est pas enregistré.</p>";
    } else {
        $sql = "INSERT INTO covoiturage (IdConducteur, DateTrajet, HeureDepart, LieuDepart, LieuArrivee, PlacesDispo, Immatriculation)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssis", $ide, $date, $heure, $depart, $arrivee, $places, $immatriculation);
        if ($stmt->execute()) {
            echo "<p class='message success'>✅ Covoiturage publié avec succès !</p>";
        } else {
            echo "<p class='message error'>❌ Erreur : " . $stmt->error . "</p>";
        }
    }
}

// --- Réservation ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reserver'])) {
    $id_covoiturage = $_POST['id_covoiturage'] ?? '';
    $dateReservation = date("Y-m-d");
    $statut = "En attente";

    if (empty($prenom)) {
        echo "<p class='message error'>Veuillez entrer votre prénom.</p>";
    } else {
        $stmt = $conn->prepare("SELECT IdE FROM etudiant WHERE Prenom = ?");
        $stmt->bind_param("s", $prenom);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) {
            $ide = $row['IdE'];
            $stmt = $conn->prepare("INSERT INTO reservation (IdE, IdCovoiturage, DateReservation, Statut, Prenom) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $ide, $id_covoiturage, $dateReservation, $statut, $prenom);

            if ($stmt->execute()) {
                echo "<p class='message success'>✅ Réservation effectuée avec succès !</p>";
            } else {
                echo "<p class='message error'>❌ Erreur de réservation : " . $stmt->error . "</p>";
            }
        } else {
            echo "<p class='message error'>❌ Étudiant non trouvé avec ce prénom.</p>";
        }
    }
}
?>

<link rel="stylesheet" href="res.css">

<?php if (!empty($prenom) && !empty($nom)): ?>
    <h2>👋 Bonjour <?= htmlspecialchars($prenom . ' ' . $nom) ?> !</h2>
<?php endif; ?>

<h2>🔍 Rechercher un trajet</h2>
<form method="get">
    <input type="text" name="depart" placeholder="Ville de départ" value="<?= htmlspecialchars($filtre_depart) ?>" required>
    <input type="text" name="arrivee" placeholder="Ville d'arrivée" value="<?= htmlspecialchars($filtre_arrivee) ?>" required>
    <button type="submit">Rechercher</button>
</form>

<h2>🚗 Covoiturages disponibles</h2>
<?php if ($res && $res->num_rows > 0): ?>
    <?php while ($row = $res->fetch_assoc()): ?>
        <form method="post">
            <p><strong><?= htmlspecialchars($row['LieuDepart']) ?> → <?= htmlspecialchars($row['LieuArrivee']) ?></strong></p>
            <p><?= $row['DateTrajet'] ?> à <?= $row['HeureDepart'] ?> | Places : <?= $row['PlacesDispo'] ?></p>
            <input type="hidden" name="id_covoiturage" value="<?= $row['IdCovoiturage'] ?>">
            <button type="submit" name="reserver">Réserver</button>
        </form>
    <?php endwhile; ?>
<?php else: ?>
    <p class="message error">Aucun trajet trouvé pour ces critères.</p>
<?php endif; ?>

<h2>📝 Publier un Covoiturage</h2>
<form method="post">
    <input type="date" name="date_trajet" required>
    <input type="time" name="heure_depart" required>
    <input type="text" name="lieu_depart" placeholder="Lieu de départ" required>
    <input type="text" name="lieu_arrivee" placeholder="Lieu d'arrivée" required>
    <input type="number" name="places" placeholder="Nombre de places" min="1" required>
    <input type="text" name="immatriculation" placeholder="Immatriculation" required>
    <button type="submit" name="publier">Publier</button>
</form>
