<?php
$host = "mysql_serv";
$db   = "wmzoughi_05";
$user = "wmzoughi";
$pass = "GRP14tropfort";
$conn = new mysqli($host, $user, $pass, $db);

$res = $conn->query("SELECT * FROM covoiturage WHERE PlacesDispo > 0 ORDER BY DateTrajet ASC");
?>

<link rel="stylesheet" href="style.css">
<h2>Réserver un Covoiturage</h2>

<?php while ($row = $res->fetch_assoc()): ?>
<form action="insert_reservation.php" method="post">
    <p><strong><?= $row['LieuDepart'] ?> → <?= $row['LieuArrivee'] ?></strong></p>
    <p><?= $row['DateTrajet'] ?> à <?= $row['HeureDepart'] ?></p>
    <p>Places dispo : <?= $row['PlacesDispo'] ?></p>
    <input type="hidden" name="id_covoiturage" value="<?= $row['IdCovoiturage'] ?>">
    <button type="submit">Réserver</button>
</form>
<?php endwhile; ?>
