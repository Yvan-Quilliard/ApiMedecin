<?php

class Appointment
{
    private $pdo;

    public function __construct()
    {
        $config = parse_ini_file("config.ini");

        try {
            $this->pdo = new \PDO("mysql:host=" . $config["host"] . ";dbname=" . $config["database"] . ";charset=utf8", $config["user"], $config["password"]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getAll()
    {
        $sql = "SELECT * FROM appointment";

        $req = $this->pdo->prepare($sql);
        $req->execute();

        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function get($id)
    {
        $sql = "SELECT * FROM appointment WHERE id = :id";

        $req = $this->pdo->prepare($sql);
        $req->bindParam(':id', $id, PDO::PARAM_INT);
        $req->execute();

        return $req->fetch(\PDO::FETCH_ASSOC);
    }

    public function getByIdPatient($id)
    {
        $sql = "SELECT * FROM appointment WHERE id = :id";

        $req = $this->pdo->prepare($sql);
        $req->bindParam(":id", $id, PDO::PARAM_INT);
        $req->execute();

        return $req->fetch(\PDO::FETCH_ASSOC);
    }

    public function getByTokenPatient($token)
    {
        $sql = "SELECT * FROM appointment WHERE id = (SELECT id FROM authentification WHERE token = :token)";

        $req = $this->pdo->prepare($sql);
        $req->bindParam(":token", $token);
        $req->execute();

        return $req->fetch(\PDO::FETCH_ASSOC);
    }

    public function exists($id)
    {
        $sql = "SELECT COUNT(*) AS nb FROM appointment WHERE id = :id";

        $req = $this->pdo->prepare($sql);
        $req->bindParam(':id', $id, PDO::PARAM_INT);
        $req->execute();

        $nb = $req->fetch(\PDO::FETCH_ASSOC)["nb"];
        if ($nb == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function insert($date, $token, $idMedecin)
    {
        $sql = "SELECT id FROM appointment WHERE idMedecin = :idMedecin AND date = :date";
        $req = $this->pdo->prepare($sql);
        $req->bindParam(':idMedecin', $idMedecin);
        $req->bindParam(':date', $date);
        $req->execute();
        $row = $req->fetch();

        if ($row != false) {
            return "Un rendez vous est déjà pris à cette horraire.";
        } else {

            $ip = $_SERVER["REMOTE_ADDR"];
            $sql = "SELECT idPatient FROM authentification WHERE token = :token AND address = :address";

            $req = $this->pdo->prepare($sql);
            $req->bindParam(':token', $token);
            $req->bindParam(':address', $ip);
            $req->execute();

            $row = $req->fetch();

            if ($row != false) {

                $sql = "INSERT INTO appointment (date, idPatient, idMedecin) VALUES (:date, :idPatient, :idMedecin)";
                $req = $this->pdo->prepare($sql);

                $req->bindParam(':date', $date);
                $req->bindParam(':idPatient', $row["idPatient"]);
                $req->bindParam(':idMedecin', $idMedecin, PDO::PARAM_INT);
                return $req->execute();
            } else {
                return false;
            }
        }
    }

    //La variable dateRdv est au format YYYY-MM-DD HH-MI-SS.
    public function update($id, $date)
    {
        $sql = 'UPDATE appointment SET date = :date WHERE id = :id';

        $req = $this->pdo->prepare($sql);
        $req->bindParam(':date', $date);
        $req->bindParam(':id', $id, PDO::PARAM_INT);
        return $req->execute();
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM appointment WHERE id = :id';

        $req = $this->pdo->prepare($sql);
        $req->bindParam(':id', $id, PDO::PARAM_INT);
        return $req->execute();
    }
}

?>