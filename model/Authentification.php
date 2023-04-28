<?php

class Authentification
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

    public function login($login, $mdp)
    {
        $sql = "SELECT id, password FROM patient WHERE login = :login";

        $req = $this->pdo->prepare($sql);
        $req->bindParam(":login", $login);
        $req->execute();

        $row = $req->fetch();

        if (($row) != false) {
            if (password_verify($mdp, $row["password"])) {
                $token = uniqid();
                $ip = $_SERVER['REMOTE_ADDR'];

                $sql = "INSERT INTO authentification VALUES (:token, :address, (SELECT id FROM patient WHERE login = :login))";

                $req = $this->pdo->prepare($sql);
                $req->bindParam(":login", $login);
                $req->bindParam(":address", $ip);
                $req->bindParam(":token", $token);

                $req->execute();

                return $token;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

?>