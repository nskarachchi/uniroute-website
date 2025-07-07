<?php
session_start();

if (isset($_POST['submit'])) {
    include "connection.php";
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $isAdmin = isset($_POST['admin']) ? 1 : 0; // Check if admin checkbox is ticked

    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($row) {
        if (password_verify($password, $row["password"])) {
            $_SESSION['email'] = $email;
            
            // Check if user is trying to log in as admin
            if ($isAdmin) {
                // Update isAdmin in database
                $update_sql = "UPDATE user SET isAdmin = 1 WHERE email = '$email'";
                mysqli_query($conn, $update_sql);
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../home/home.php");
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" /> 
    <title>Sign-in</title>
    <link rel="icon" href="../resources/logo.png">
    <link rel="stylesheet" href="../user/signIn.css">
</head>
<body>
    <div>
        <img src="../resources/logo.png" class="logoUP">
    
        <div class="wrap">
            <div class="form-box login">
                <h2>Sign In</h2>
                <?php if (isset($error)): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <form action="" method="post">
                    <div class="input-box">
                        <span class="material-symbols-outlined">mail</span>
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-box">
                        <span class="material-symbols-outlined">lock</span>
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <div class="rem-forg">
                        <label>
                            <input type="checkbox" name="admin">Log In As Administrator
                        </label>
                        <a href="../user/forgot_password.php">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn" name="submit">Login</button>
                    <div class="login-reg">
                        <p>Don't have an Account?
                            <a href="../user/register.php" class="reg-link">Register</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>