<?php
session_start();
include 'connection.php';

// Load PHPMailer to send a mail
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Prevent logged-in users from accessing this page
if (isset($_SESSION['email'])) {
    header("Location: ../home/home.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists in the user table
    $sql = "SELECT id FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32)); // 64-character token
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        // Delete any existing reset token for this email
        $delete_sql = "DELETE FROM password_resets WHERE email = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("s", $email);
        $delete_stmt->execute();

        // Store the new token in the password_resets table
        $insert_sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sss", $email, $token, $expires_at);

        if ($insert_stmt->execute()) {
            // Generating reset link
            $reset_link = "http://localhost/uniRoute-Web/user/reset_password.php?token=" . $token;

            // Send the reset link to email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'brendon5860@gmail.com';
                $mail->Password = 'fkgehgastzzkrghi';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // send mail to users ac
                $mail->setFrom('no-reply@uniroute.com', 'UniRoute');
                $mail->addAddress($email); 

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request - UniRoute';
                $mail->Body = "Dear User,<br><br>"
                            . "We received a request to reset your UniRoute password. Click the link below to reset your password:<br>"
                            . "<a href='$reset_link'>Reset Password</a><br><br>"
                            . "This link will expire in 1 hour. If you did not request a password reset, please ignore this email.<br><br>"
                            . "Best regards,<br>UniRoute Team";
                $mail->AltBody = "Dear User,\n\n"
                               . "We received a request to reset your UniRoute password. Copy and paste the following link into your browser to reset your password:\n"
                               . "$reset_link\n\n"
                               . "This link will expire in 1 hour. If you did not request a password reset, please ignore this email.\n\n"
                               . "Best regards,\nUniRoute Team";

                $mail->send();
                $message = "A password reset link has been sent to your email.";
            } catch (Exception $e) {
                $error = "Failed to send the reset email: " . $mail->ErrorInfo;
            }
        } else {
            $error = "Failed to generate a reset link: " . $conn->error;
        }
    } else {
        $error = "No account found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - UniRoute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            padding: 30px;
            width: 100%;
            max-width: 420px;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40,167,69,.25);
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="card">
    <h3 class="text-center mb-4">Forgot Password</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success text-center" role="alert">
            <?= $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-success">Send Reset Link</button>
        </div>
    </form>

    <div class="back-link mt-3">
        <a href="signIn.php" class="text-decoration-none">← Back to Sign In</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
