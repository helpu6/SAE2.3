<?php
class DB {
    private $db;

    public function connexion() {
        try {
            // Paramètres de connexion
            $host = 'mysql_serv'; // vérifie que le nom d'hôte est correct
            $dbname = 'wmzoughi_05';
            $user = 'wmzoughi';
            $pass = 'GRP14tropfort';

            // Chaîne de connexion DSN
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

            // Création de la connexion PDO
            $this->db = new PDO($dsn, $user, $pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->db;

        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
}
?>
