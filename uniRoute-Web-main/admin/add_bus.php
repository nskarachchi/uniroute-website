<?php
include 'db.php';

// Safely get POST values
$bus_id = $_POST['bus_id'] ?? '';
$latitude = $_POST['latitude'] ?? 0;
$longitude = $_POST['longitude'] ?? 0;
$contact_number = $_POST['contact_number'] ?? '';
$email = $_POST['driver_email'] ?? '';
$status = $_POST['status'] ?? 'inactive';

$sql = "INSERT INTO buses (bus_id, latitude, longitude, contact_number, driver_email, status)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

$stmt->bind_param("sddsss", $bus_id, $latitude, $longitude, $contact_number, $email, $status);

if ($stmt->execute()) {
    echo "New Bus Added Successfully!";
} else {
    echo "Error Adding Bus: " . $stmt->error;
}

$conn->close();
?>
