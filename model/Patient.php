<?php

class Patient
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
        $sql = 'SELECT * FROM patient';

        $req = $this->pdo->prepare($sql);
        $req->execute();

        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = 'SELECT * FROM patient WHERE id = :id';

        $req = $this->pdo->prepare($sql);
        $req->bindParam(':id', $id, PDO::PARAM_INT);
        $req->execute();

        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getByLastName($lastName)
    {
        $sql = 'SELECT * FROM patient WHERE name = :lastName';

        $req = $this->pdo->prepare($sql);
        $req->bindParam(':lastName', $lastName, PDO::PARAM_INT);
        $req->execute();

        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getByFirstName($firstName)
    {
        $sql = 'SELECT * FROM patient WHERE firstName = :firstName';

        $req = $this->pdo->prepare($sql);
        $req->bindParam(':firstName', $firstName, PDO::PARAM_INT);
        $req->execute();

        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function insert($lastName, $firstName, $street, $zipCode, $city, $phone, $login, $password)
    {
        $sql = "INSERT INTO patient (lastName, firstName, street, zipCode, city, phone, login, password) VALUES (:lastName, :firstName, :street, :zipCode, :city, :phone, :login, :password)";

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $req = $this->pdo->prepare($sql);
        $req->bindParam(':lastName', $lastName);
        $req->bindParam(':firstName', $firstName);
        $req->bindParam(':street', $street);
        $req->bindParam(':zipCode', $zipCode);
        $req->bindParam(':city', $city);
        $req->bindParam(':phone', $phone);
        $req->bindParam(':login', $login);
        $req->bindParam(':password', $passwordHash);

        return $req->execute();
    }

    public function exists($id)
    {
        $sql = "SELECT COUNT(*) AS nb FROM patient WHERE id = :id";

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

    public function exist($token)
    {
        $sql = "SELECT COUNT(*) AS nb FROM authentification WHERE token = :token";

        $req = $this->pdo->prepare($sql);
        $req->bindParam(':token', $token, PDO::PARAM_STR);
        $req->execute();

        $nb = $req->fetch(\PDO::FETCH_ASSOC)["nb"];

        if ($nb == 1) {
            return true;
        } else {
            return false;
        }
    }

}

?>