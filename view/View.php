<?php

class View
{

    public function error404()
    {
        $response = array("message" => "Erreur 404 : ressource introuvable pour l'URL ou la méthode demandée");
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *");
        $response = json_encode($response);
        echo $response;
    }

    public function transformerJson($data)
    {
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *");
        $data = json_encode($data);
        echo $data;
    }
}

?>