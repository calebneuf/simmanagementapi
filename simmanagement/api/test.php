<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include("config.php");

if (!isset($_GET['license'])) {
    http_response_code(404);
    echo json_encode(array("message" => "No license key provided."));
    exit;
}

if($license != $_GET['license']) {
    http_response_code(404);
    echo json_encode(array("message" => "License key invalid."));
    exit;
}

if ($license == "xxx") {
    http_response_code(404);
    echo json_encode(array("message" => "License key not configured."));
    exit;
}

http_response_code(200);
echo json_encode(array("message" => "Successful connection!"));
exit;

?>
