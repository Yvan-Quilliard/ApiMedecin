<?php

class Controller
{

    public function error404()
    {
        http_response_code(404);
        (new View)->erreur404;
    }

    public function checkAttributsJson($objetJson, $arrayAttributs)
    {
        $check = true;
        foreach ($arrayAttributs as $attribut) {
            if (!isset($objetJson->$attribut)) {
                $check = false;
            }
        }
        return $check;
    }

    public function indexAppointment()
    {
        $data = null;

        if (isset($_GET["id"])) {
            if ((new Appointment)->exists($_GET["id"])) {
                http_response_code(200);
                $data = (new Appointment)->get($_GET["id"]);
            } else {
                http_response_code(404);
                $data = array("message" => "Rendez vous introuvable");
            }
        } elseif (isset($_POST["token"])) {
            http_response_code(200);
            $data = (new Appointment)->getBytokenPatient($_POST["token"]);
        } else {
            http_response_code(200);
            $data = (new Appointment)->getAll();
        }

        (new View)->transformerJson($data);
    }

    public function createAppointment()
    {
        $data = json_decode(file_get_contents("php://input"));
        $response = null;

        if ($data === null) {
            http_response_code(400);
            $response = array("message" => "JSON envoyé incorrect");
        } else {
            $attributsRequired = array("date", "token", "idMedecin");
            if ($this->checkAttributsJson($data, $attributsRequired)) {
                if ((new Patient)->exist($data->token)) {
                    $result = (new Appointment)->insert($data->date, $data->token, $data->idMedecin);

                    if ($result != false) {
                        http_response_code(201);
                        $response = array("message" => "Ajout effectué avec succès");
                    } else {
                        http_response_code(500);
                        $response = array("message" => "Une erreur interne est survenue");
                    }
                } else {
                    http_response_code(400);
                    $response = array("message" => "La patient n'existe pas");
                }
            } else {
                http_response_code(400);
                $response = array("message" => "Données manquantes");
            }
        }

        (new View)->transformerJson($response);
    }

    public function updateAppointment()
    {
        $data = json_decode(file_get_contents("php://input"));
        $response = null;

        if ($data === null) {
            http_response_code(400);
            $response = array("message" => "JSON envoyé incorrect", "data" => $data);
        } else {
            $attributsRequired = array("id", "date");
            if ($this->checkAttributsJson($data, $attributsRequired)) {
                if ((new Appointment)->exists($data->id)) {
                    $result = (new Appointment)->update($data->id, $data->date);

                    if ($result != false) {
                        http_response_code(200);
                        $response = array("message" => "Modification effectuée avec succès");
                    } else {
                        http_response_code(500);
                        $response = array("message" => "Une erreur interne est survenue");
                    }
                } else {
                    http_response_code(400);
                    $response = array("message" => "Le appointment spécifié n'existe pas");
                }
            } else {
                http_response_code(400);
                $response = array("message" => "Données manquantes");
            }
        }

        (new View)->transformerJson($response);
    }

    public function destroyAppointment()
    {
        $data = json_decode(file_get_contents("php://input"));
        $response = null;

        if ($data === null) {
            http_response_code(400);
            $response = array("message" => "JSON envoyé incorrect");
        } else {
            $attributsRequired = array("id");
            if ($this->checkAttributsJson($data, $attributsRequired)) {
                if ((new Appointment)->exists($data->id)) {
                    $result = (new Appointment)->delete($data->id);

                    if ($result != false) {
                        http_response_code(200);
                        $response = array("message" => "Suppression effectuée avec succès");
                    } else {
                        http_response_code(500);
                        $response = array("message" => "Une erreur interne est survenue");
                    }
                } else {
                    http_response_code(400);
                    $response = array("message" => "Le appointment spécifiée n'existe pas");
                }
            } else {
                http_response_code(400);
                $response = array("message" => "Données manquantes");
            }
        }

        (new View)->transformerJson($response);
    }

    public function indexPatient()
    {
        $data = null;

        if (isset($_GET["id"])) {
            if ((new Patient)->exists($_GET["id"])) {
                http_response_code(200);
                $data = (new Patient)->getById($_GET["id"]);
            } else {
                http_response_code(404);
                $data = array("message" => "Patient introuvable");
            }
        } else {
            http_response_code(200);
            $data = (new Patient)->getAll();
        }

        (new View)->transformerJson($data);
    }

    public function createPatient()
    {
        $data = json_decode(file_get_contents("php://input"));
        $response = null;

        if ($data === null) {
            http_response_code(400);
            $response = array("message" => "JSON envoyé incorrect", "donnees" => $data);
        } else {
            $attributsRequired = array("lastName", "firstName", "street", "zipCode", "city", "phone", "login", "password");
            if ($this->checkAttributsJson($data, $attributsRequired)) {

                if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{13,}$/', $data->password)) {
                    $result = (new Patient)->insert($data->lastName, $data->firstName, $data->city, $data->zipCode, $data->city, $data->phone, $data->login, $data->password);
                    if ($result != false) {
                        http_response_code(201);
                        $response = array("message" => "Patient inscrit avec succès");
                    } else {
                        http_response_code(500);
                        $response = array("message" => "Une erreur interne est survenue");
                    }
                } else {
                    http_response_code(400);
                    $response = array("message" => "Le mot de passe doit contenir au moins 13 caractères dont une majuscule, une minuscule, un chiffre et un caractère spécial");
                }
            } else {
                http_response_code(400);
                $response = array("message" => "Données manquantes");
            }
        }

        (new View)->transformerJson($response);

    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"));
        $response = null;

        if ($data === null) {
            http_response_code(400);
            $response = array("message" => "JSON envoyé incorrect");
        } else {
            $attributsRequis = array("login", "password");
            if ($this->checkAttributsJson($data, $attributsRequis)) {

                $result = null;

                $result = (new Authentification)->login($data->login, $data->password);

                if ($result != null) {
                    http_response_code(201);
                    $response = array("message" => "Vous êtes correctement connecté", "token" => $result);
                } else {
                    http_response_code(500);
                    $response = array("message" => "Une erreur interne est survenue");
                }
            } else {
                http_response_code(400);
                $response = array("message" => "Données manquantes");
            }
        }

        (new View)->transformerJson($response);
    }
}

?>