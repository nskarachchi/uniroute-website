<?php
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_GET['route_id']) || empty($_GET['route_id'])) {
    echo json_encode(["error" => "Route ID is missing"]);
    exit();
}

$route_id = intval($_GET['route_id']);

$sql = "SELECT bus_id, latitude, longitude, status FROM buses WHERE route_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL error"]);
    exit();
}

$stmt->bind_param("i", $route_id);
$stmt->execute();
$result = $stmt->get_result();

$buses = [];
while ($row = $result->fetch_assoc()) {
    $buses[] = $row;
}

if (empty($buses)) {
    echo json_encode(["error" => "No buses found for route ID $route_id"]);
} else {
    echo json_encode($buses);
}

$stmt->close();
$conn->close();
?>
