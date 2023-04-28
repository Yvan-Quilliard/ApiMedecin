<?php

session_start();

$config = parse_ini_file("config.ini");
try {
    $pdo = new \PDO("mysql:host=" . $config["host"] . ";dbname=" . $config["database"] . ";charset=utf8", $config["user"], $config["password"]);
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *");
    echo '{ "message":"Erreur de connexion à la base de données" }';
    exit;
}

require("control/Controlleur.php");
require("view/View.php");
require("model/Patient.php");
require("model/Appointment.php");
require("model/Authentification.php");

if (isset($_GET["action"])) {
    switch ($_GET["action"]) {

        case "appointment":
            switch ($_SERVER["REQUEST_METHOD"]) {
                case "GET":
                    (new Controller)->indexAppointment();
                    break;
                case "POST":
                    (new Controller)->createAppointment();
                    break;
                case "PUT":
                    (new Controller)->updateAppointment();
                    break;
                case "DELETE":
                    (new Controller)->destroyAppointment();
                    break;
                default:
                    (new Controller)->error404();
                    break;
            }
            break;

        case "patient":
            switch ($_SERVER["REQUEST_METHOD"]) {
                case "GET":
                    (new Controller)->indexPatient();
                    break;
                case "POST":
                    (new Controller)->createPatient();
                    break;
                default:
                    (new Controller)->error404();
                    break;
            }
            break;

        case "authentification":
            switch ($_SERVER["REQUEST_METHOD"]) {
                case 'POST':
                    (new Controller)->login();
                    break;

                default:
                    (new Controller)->error404();
                    break;
            }
            break;
        default:
            (new Controller)->error404();
            break;
    }
} else {
    // Pas d'action précisée = erreur 404
    (new Controller)->error404();
}
?>