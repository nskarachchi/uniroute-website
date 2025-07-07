<?php
include 'db.php';

$sql = "SELECT user_name FROM loged_user ";
$result = $conn->query($sql);

// Check if query ran successfully
if ($result && $result->num_rows > 0) {
    echo $result->fetch_assoc()['user_name'];
} else {
    echo "Guest"; // Fallback
}

$conn->close();
?>
