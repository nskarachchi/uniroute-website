<?php
include 'connection.php';

header('Content-Type: application/json');

try {
    // Query to get routes from your database
    $sql = "SELECT * FROM route"; 
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    $routes = [];
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
    
    echo json_encode($routes);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>