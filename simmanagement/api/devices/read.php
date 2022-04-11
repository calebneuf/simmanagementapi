<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include("../config.php");

if (!isset($_GET['license'])) {
    http_response_code(404);
    echo json_encode(array("message" => "No license key provided."));
    exit;
}

if ($license != $_GET['license']) {
    http_response_code(404);
    echo json_encode(array("message" => "License invalid."));
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_escape_string($conn, $_GET['id']);

    #Find all entries using sim_number field, and sanitize inputs to prevent MySQL Injection
    $sqlQuery = "SELECT id, name, imei, sim_number, active FROM devices WHERE id = ?";
    $stmt = $conn->prepare($sqlQuery);
    $stmt->bind_param("d", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $imei, $sim_number, $active);

    #If there are returned results, loop through and return first result to a new Device object
    if ($stmt->num_rows() != 0) {
        $readArray = array();
        while ($stmt->fetch()) {
            $readArray[] = array(
                "id" => $id,
                "name" => $name,
                "imei" => $imei,
                "sim_number" => $sim_number,
                "active" => $active,
                "owners" => getOwners($id)
            );

            http_response_code(200);

            echo json_encode(utf8ize($readArray));
        }
    } else {
        http_response_code(404);

        echo json_encode(array("message" => "Device not found."));
    }
} else {
    $sqlQuery = "SELECT id, name, imei, sim_number, active FROM devices";
    $stmt = $conn->prepare($sqlQuery);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $imei, $sim_number, $active);

    #If there are returned results, loop through and return first result to a new Device object
    if ($stmt->num_rows() != 0) {
        $readArray = array();
        while ($stmt->fetch()) {
            $readArray[] = array(
                "id" => $id,
                "name" => $name,
                "imei" => $imei,
                "sim_number" => $sim_number,
                "active" => $active,
                "owners" => getOwners($id)
            );
        }
        http_response_code(200);
        $json = json_encode(utf8ize($readArray));
        if($json) {
            echo $json;
        } else {
            echo json_last_error_msg();
        }
    }
}

function getOwners($id)
{
    #Connect to database
    global $conn;

    #Find all entries using sim_number field, and sanitize inputs to prevent MySQL Injection
    $sqlQuery = "SELECT user_id FROM user_device_pivot WHERE device_id = ?";
    $stmt = $conn->prepare($sqlQuery);
    $stmt->bind_param("d", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id);

    $owners = array();

    #If there are returned results, loop through and return first result to a new Device object
    if ($stmt->num_rows() != 0) {
        while ($stmt->fetch()) {
            $owners[] = $user_id;
        }
    }
    return $owners;
}

function utf8ize( $mixed ) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
}
