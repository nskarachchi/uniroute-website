<?php
include 'connection.php';

$sql = "SELECT * FROM buses";
$result = $conn->query($sql);

$buses = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $buses[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($buses);

$conn->close();
?>
