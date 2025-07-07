<?php
include 'db.php'; 

header('Content-Type: application/json');

$sql = "SELECT bus_id, latitude, longitude,contact_number, driver_email, status FROM buses";
$result = $conn->query($sql);

$buses = [];

while ($row = $result->fetch_assoc()) {
    $buses[] = $row;
}

echo json_encode($buses);

$conn->close();
?>
