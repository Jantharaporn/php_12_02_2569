<?php
$host = "localhost";
$username = "it67040233119";
$password = "M1Q8L9N6";
$db = "it67040233119";

$conn = new mysqli($host, $username, $password, $db);

if ($conn->connect_error) {
    die(json_encode([
        "status" => 500,
        "message" => "Database connection failed"
    ]));
}
?>
