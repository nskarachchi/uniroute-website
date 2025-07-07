<?php
$servername = "localhost";
$username = "root";
$password = "pass";
$dbname = "uniroot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $stmt = $conn->prepare("INSERT INTO feedback (name, email, contact, rating, comments) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $name, $email, $contact, $rating, $comments);

    if ($stmt->execute()) {
        echo "<script>alert('Feedback submitted successfully!');
        window.location.href = '../feedback/feedback.php';
        </script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}


?>
