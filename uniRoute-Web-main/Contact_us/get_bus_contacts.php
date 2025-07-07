<?php
include 'db.php';
header('Content-Type: application/json');

$sql = "SELECT bus_id, route_id, contact_number, status FROM buses WHERE contact_number IS NOT NULL";
$result = $conn->query($sql);

$buses = [];
while ($row = $result->fetch_assoc()) {
    $buses[] = $row;
}

echo json_encode($buses);
$conn->close();
?>
