<?php
$host = "localhost"; // change to the deployed db
$db_name = "venuebook"; // change to the deployed db
$username = "root"; // change to the deployed db
$password = ""; // change to the deployed db
$conn = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
}
?>