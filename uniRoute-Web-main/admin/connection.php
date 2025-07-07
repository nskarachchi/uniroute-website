<?php
// connection.php

$host = 'localhost';
$db   = 'uniroot';
$user = 'root';
$pass = 'pass';


$dsn = "mysql:host=$host;dbname=$db";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
