<?php
$host = "localhost";
$user = "root";
$pass = "pass";
$dbname = "uniroot";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
