<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../user/signIn.php");
    exit();
}

$loggedInEmail = $_SESSION['email'];

// Get user's name
$userName = "User";
$sql = "SELECT username FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $userName = $row['username'];
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="style.css"/>
    <script src="new.js"></script>
</head>
<body>
    <?php include '../common/header.php'; ?>


    <main>
        <div class="feedback-container">
            <h2>We Value Your Feedback</h2>
            <p>Please take a moment to share your experience with the UniRoute shuttle system.</p>
            
        <form id="feedback-form" action="index.php" method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required placeholder="Your full name">
    
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="Your email address">
    
            <label for="contact">Contact Number</label>
            <input type="number" id="contact" name="contact" required maxlength="10" required placeholder="Your contact number">
    
            <label for="rating">Rating</label>
            <select id="rating" name="rating" required>
                <option value="5">5 - Excellent</option>
                <option value="4">4 - Good</option>
                <option value="3">3 - Average</option>
                <option value="2">2 - Poor</option>
                <option value="1">1 - Terrible</option>
            </select>
    
            <label for="comments">Additional Comments</label>
            <textarea id="comments" name="comments" rows="4" placeholder="Your feedback..." required></textarea>
    
            <button type="submit" class="submit-btn">Submit Feedback</button>
        </form>

            
        </div>
    </main>


    <?php include '../common/footer.html'; ?>


    <script>
        document.getElementById("feedback-form").addEventListener("submit", function(event) {
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let contact = document.getElementById("contact").value.trim();
    let comments = document.getElementById("comments").value.trim();

    if (name === "" || email === "" || comments === "") {
        alert("Please fill out all the fields before submitting.");
        event.preventDefault();
    }
});

    </script>
</body>
</html>
