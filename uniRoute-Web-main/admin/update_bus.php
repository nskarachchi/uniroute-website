<?php
include 'db.php';

$bus_id = $_POST['bus_id'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$contact_number = $_POST['contact_number'];
$email = $_POST['driver_email'];
$status = $_POST['status'];

$sql = "UPDATE buses SET latitude = ?, longitude = ?, contact_number = ?,driver_email =?, status = ? WHERE bus_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ddssss", $latitude, $longitude, $contact_number,$email, $status, $bus_id);

if ($stmt->execute()) {
    echo "Bus Updated Successfully!";
} else {
    echo "Error Updating Bus!";
}

$conn->close();
?>
