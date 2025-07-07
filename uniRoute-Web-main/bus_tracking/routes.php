<?php
include 'connection.php';

$sql = "SELECT route_id, route FROM route";
$result = $conn->query($sql);

$routes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $routes[] = $row;
    }
} else {
    $routes = ["error" => "No routes found"];
}

header('Content-Type: application/json');
echo json_encode($routes);
$conn->close();
?>