<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$license = "xxx";

$conn = new mysqli("localhost", "root");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn->select_db("gpswox_web");

?>