<?php
include 'connection.php';

header('Content-Type: application/json');

$sql = "SELECT bus_id, latitude, longitude, contact_number, status FROM buses WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
$result = $conn->query($sql);

$buses = [];

while ($row = $result->fetch_assoc()) {
    $buses[] = [
        "bus_id" => $row["bus_id"],
        "latitude" => $row["latitude"],
        "longitude" => $row["longitude"],
        "contact_number" => $row["contact_number"],
        "status" => $row["status"]
    ];
}

echo json_encode($buses);
$conn->close();
?>
