<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: signIn.php");
    exit();
}

$loggedInEmail = $_SESSION['email'];

include 'connection.php';

// Fetch user details using the email stored in session
$sql = "SELECT id, username, email, contactNum, image FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $fetch = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_destroy();
    header("Location: signIn.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile - UniRoute</title>
    <link rel="icon" href="../resources/logo.png">
    <!-- Google Fonts for modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Material Symbols for icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f4f4f4, #e0e0e0);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .profile-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
            position: relative;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-container h2 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .profile-pic-container {
            position: relative;
            margin-bottom: 20px;
        }

        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #28a745;
            transition: transform 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.05);
        }

        .profile-details {
            margin-bottom: 30px;
        }

        .profile-details h3 {
            font-size: 20px;
            font-weight: 500;
            color: #333;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 10px 0;
            font-size: 14px;
            color: #555;
        }

        .detail-item .icon {
            color: #28a745;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }

        .btn, .delete-btn, .link-btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
        }

        .btn {
            background: #28a745;
            color: white;
            border: none;
        }

        .btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .delete-btn {
            background: #d9534f;
            color: white;
        }

        .delete-btn:hover {
            background: #c9302c;
            transform: translateY(-2px);
        }

        .delete-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .link-btn {
            background: #007bff;
            color: white;
        }

        .link-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .auth-links {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        .auth-links a {
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-container {
                padding: 20px;
                max-width: 90%;
            }

            .profile-pic {
                width: 100px;
                height: 100px;
            }

            .btn-container {
                flex-direction: column;
                gap: 15px;
            }

            .btn, .delete-btn, .link-btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .profile-container h2 {
                font-size: 20px;
            }

            .profile-details h3 {
                font-size: 18px;
            }

            .detail-item {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Your Profile</h2>

        <!-- Profile Picture -->
        <div class="profile-pic-container">
            <?php
            if (isset($fetch['image']) && !empty($fetch['image'])) {
                echo '<img src="../uploaded_img/' . htmlspecialchars($fetch['image']) . '" alt="Profile Image" class="profile-pic">';
            } else {
                echo '<img src="../image/default-avatar.png" alt="Default Avatar" class="profile-pic">';
            }
            ?>
        </div>

        <!-- Profile Details -->
        <div class="profile-details">
            <h3><?php echo htmlspecialchars($fetch['username']); ?></h3>
            <div class="detail-item">
                <span class="material-symbols-outlined icon">email</span>
                <span>Email: <?php echo htmlspecialchars($fetch['email']); ?></span>
            </div>
            <div class="detail-item">
                <span class="material-symbols-outlined icon">phone</span>
                <span>Contact: <?php echo htmlspecialchars($fetch['contactNum'] ?: 'Not provided'); ?></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="btn-container">
            <a href="../user/updateProfile.php" class="btn">Update Profile</a>
            <a href="?logout=true" class="delete-btn" id="logoutBtn" onclick="handleLogout(event)">Logout</a>
            <a href="../home/home.php" class="link-btn">Back to Home</a>
        </div>

        <!-- Login/Register Links -->
        <div class="auth-links">
            <p>New <a href="../user/signIn.php">login</a> or <a href="../user/register.php">register</a></p>
        </div>
    </div>

    <script>
        // Handle logout with loading state
        function handleLogout(event) {
            event.preventDefault();
            const logoutBtn = document.getElementById('logoutBtn');
            logoutBtn.textContent = 'Logging out...';
            logoutBtn.disabled = true;
            window.location.href = logoutBtn.href;
        }
    </script>
</body>
</html>