<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['sender_name'];
    $bus_id = $_POST['bus_id'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (sender_name, bus_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $bus_id, $message);

    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Failed to send message.";
    }

    $stmt->close();
    $conn->close();
}
?>
