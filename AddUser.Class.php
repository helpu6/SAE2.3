<?php

class AddUser {
    private $db;

    public function __construct($pdo) {
        // Enable error reporting for debugging
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db = $pdo;
    }

    public function getIdFormation($nom, $formation, $groupe, $sous_groupe) {
        $sql = "SELECT IdIUT FROM etablissement 
                WHERE Nom = '$nom' AND Formation = '$formation' AND Groupe = '$groupe' AND Sous_groupe = '$sous_groupe'";
        $result = $this->db->query($sql);
        $row = $result ? $result->fetch(PDO::FETCH_ASSOC) : null;
        return $row ? $row['IdIUT'] : null;
    }

    public function getIdStudent($name, $firstname) {
        $sql = "SELECT IdE FROM etudiant WHERE Nom = '$name' AND Prenom = '$firstname'";
        $result = $this->db->query($sql);
        $row = $result ? $result->fetch(PDO::FETCH_ASSOC) : null;
        return $row ? $row['IdE'] : null;
    }

    public function addCar($immatriculation, $type, $marque, $modele, $places, $IdE, $en_regle) {
        $sql = "INSERT INTO vehicule (Immatriculation, IdE, Type, Marque, Modele, Places, En_regle)
                VALUES ('$immatriculation', '$IdE', '$type', '$marque', '$modele', '$places', '$en_regle')";
        $this->db->exec($sql);
    }

    public function addPapers($immatriculation, $cg, $ct, $assurance, $permis, $points_permis) {
        $sql = "INSERT INTO papiers (Immatriculation, Carte_Grise, Controle_Technique, Assurance, Permis, Points_Permis)
                VALUES ('$immatriculation', '$cg', '$ct', '$assurance', '$permis', '$points_permis')";
        $this->db->exec($sql);
    }

    public function papersAreInLaw($immatriculation) {
        $sql = "SELECT Carte_Grise, Controle_Technique, Assurance, Permis, Points_Permis
                FROM papiers WHERE Immatriculation = '$immatriculation'";
        $result = $this->db->query($sql);
        $papers = $result ? $result->fetch(PDO::FETCH_ASSOC) : [];

        $en_regle = 1;
        foreach ($papers as $value) {
            if ((int)$value < 1) {
                $en_regle = 0;
                break;
            }
        }

        $updateSql = "UPDATE vehicule SET En_regle = '$en_regle' WHERE Immatriculation = '$immatriculation'";
        $this->db->exec($updateSql);

        return $en_regle;
    }

    public function existByName($name, $firstname) {
        $sql = "SELECT * FROM etudiant WHERE Nom = '$name' AND Prenom = '$firstname'";
        $result = $this->db->query($sql);
        return $result && $result->fetch() ? true : false;
    }

    public function addStudent($name, $firstname, $domicile, $IUT, $formation, $groupe, $sous_groupe,
                                $immatriculation, $type, $marque, $modele, $cg, $ct, $assurance, $permis,
                                $point_permis, $places) {

        // Debug print input values
        echo "<pre>";
        echo "DEBUG: Creating student with data:\n";
        echo "Name: $name, Firstname: $firstname\n";
        echo "IUT: $IUT, Formation: $formation, Groupe: $groupe, Sous-groupe: $sous_groupe\n";
        echo "</pre>";

        $IdIUT = $this->getIdFormation($IUT, $formation, $groupe, $sous_groupe);
        if ($IdIUT === null) {
            echo "❌ Erreur : formation non trouvée.";
            return;
        }

        // Insert the student
        try {
            $sql = "INSERT INTO etudiant (IdIUT, Nom, Prenom, Domicile) 
                    VALUES ('$IdIUT', '$name', '$firstname', '$domicile')";
            $this->db->exec($sql);
        } catch (PDOException $e) {
            echo "❌ Erreur lors de l'insertion de l'étudiant : " . $e->getMessage();
            return;
        }

        // Get the new student ID
        $IdE = $this->getIdStudent($name, $firstname);
        if (!$IdE) {
            echo "❌ Erreur : impossible de récupérer l'ID étudiant.";
            return;
        }

        // Optional: Add vehicle
        if (!empty($immatriculation) && strlen($immatriculation) > 2) {
            try {
                $this->addPapers($immatriculation, $cg, $ct, $assurance, $permis, $point_permis);
                $this->addCar($immatriculation, $type, $marque, $modele, $places, $IdE, 0);
                $this->papersAreInLaw($immatriculation);
            } catch (PDOException $e) {
                echo "❌ Erreur lors de l'ajout du véhicule ou des papiers : " . $e->getMessage();
                return;
            }
        }

        echo "✅ Étudiant ajouté avec succès.";
    }
}
?>
