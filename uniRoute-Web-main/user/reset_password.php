<?php
include 'connection.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    die("Invalid request.");
}

$stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $email = $row['email'];
} else {
    die("Invalid or expired token.");
}

$passwordUpdated = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $passwordUpdated = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - UniRoute</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="card shadow p-4" style="max-width: 450px; width: 100%;">
    <h3 class="text-center mb-4">Reset Password</h3>

    <?php if ($passwordUpdated): ?>
        <div class="alert alert-success text-center">
            Your password has been updated. You can now
            <a href="../user/SignIn.php" class="alert-link">login</a>.
        </div>
    <?php else: ?>
        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="8" placeholder="Enter new password">
                <div class="form-text">Password must be at least 8 characters long.</div>
            </div>
            <button type="submit" class="btn btn-success w-100">Update Password</button>
        </form>
    <?php endif; ?>

    <div class="text-center mt-3">
        <a href="../user/SignIn.php" class="text-decoration-none">← Back to Sign In</a>
    </div>
</div>

</body>
</html>
