<?php
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_GET['route_id'])) {
    echo json_encode(['error' => 'Route ID is required']);
    exit;
}

$route_id = intval($_GET['route_id']);

try {
    // Get all buses assigned to this route with their current locations
    $sql = "SELECT bus_id, latitude, longitude, status FROM buses WHERE route_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $buses = [];
    while ($row = $result->fetch_assoc()) {
        $buses[] = $row;
    }
    
    echo json_encode($buses);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>