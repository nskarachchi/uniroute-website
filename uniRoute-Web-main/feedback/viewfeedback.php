<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uniroute";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM feedback ORDER BY submitted_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='feedback'>";
        echo "<h3>" . $row['name'] . "</h3>";
        echo "<p><strong>Rating:</strong> " . $row['rating'] . " / 5</p>";
        echo "<p><strong>Email:</strong> " . $row['email'] . "</p>";
        echo "<p><strong>Contact:</strong> " . $row['contact'] . "</p>";
        echo "<p><strong>Comments:</strong> " . $row['comments'] . "</p>";
        echo "<p><strong>Submitted At:</strong> " . $row['submitted_at'] . "</p>";
        echo "</div><hr>";
    }
} else {
    echo "No feedback available.";
}

$conn->close();
?>
