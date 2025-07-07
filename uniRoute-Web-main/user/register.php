<?php
session_start();

if (isset($_POST['submit'])) {
    include "connection.php";
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contactNum = mysqli_real_escape_string($conn, $_POST['contactNum']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);

    //username validate
    if (!preg_match('/^[a-zA-Z0-9]{3,}$/', $username)) {
        echo "<script>alert('Username must be at least 3 characters and contain only letters and numbers!'); window.location.href='register.php';</script>";
        exit();
    }

    //contact num validate
    if (!preg_match('/^[0-9]{10}$/', $contactNum)) {
        echo "<script>alert('Contact number must be 10 digits!'); window.location.href='register.php';</script>";
        exit();
    }

    $sql = "SELECT * FROM user WHERE username = '$username' OR email = '$email'";
    $result = mysqli_query($conn, $sql);
    $userExists = mysqli_num_rows($result) > 0;

    if (!$userExists) {
        if ($password === $cpassword) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO user(username, email, password, contactNum) 
                    VALUES('$username', '$email', '$hash', '$contactNum')";
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Registration successful!'); window.location.href='../home/home.php';</script>";
            } else {
                echo "<script>alert('Registration failed!'); window.location.href='register.php';</script>";
            }
        } else {
            echo "<script>alert('Passwords do not match!'); window.location.href='register.php';</script>";
        }
    } else {
        echo "<script>alert('Username or email already exists!'); window.location.href='register.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-Up</title>
    <link rel="icon" href="../resources/logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('../resources/nsbmBG.jpg') no-repeat center/cover;
        }

        .logoUP {
            width: 100px;
            display: block;
            margin: 0 auto 20px;
        }

        .wrap {
            max-width: 400px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .form-box h2 {
            font-size: 2em;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .input-box {
            position: relative;
            margin: 30px 0;
        }

        .input-box input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            background: transparent;
            border: none;
            border-bottom: 2px solid #333;
            outline: none;
            font-size: 1em;
            color: #333;
        }

        .input-box label {
            position: absolute;
            top: 50%;
            left: 40px;
            transform: translateY(-50%);
            font-size: 1em;
            color: #333;
            pointer-events: none;
            transition: 0.3s;
        }

        .input-box input:focus ~ label,
        .input-box input:valid ~ label {
            top: -5px;
            font-size: 0.8em;
        }

        .input-box span.material-symbols-outlined {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            font-size: 1.2em;
            color: #333;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #0056b3;
        }

        .login-reg {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #333;
        }

        .login-reg a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .login-reg a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div>
        <img src="../resources/logo.png" class="logoUP">
        <div class="wrap">
            <div class="form-box login">
                <h2>Sign Up</h2>
                <form action="" method="post">
                    <div class="input-box">
                        <span class="material-symbols-outlined">person</span>
                        <input type="text" name="username" required>
                        <label>Username</label>
                    </div>
                    <div class="input-box">
                        <span class="material-symbols-outlined">mail</span>
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-box">
                        <span class="material-symbols-outlined">phone</span>
                        <input type="text" name="contactNum" required>
                        <label>Contact Number</label>
                    </div>
                    <div class="input-box">
                        <span class="material-symbols-outlined">lock</span>
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <div class="input-box">
                        <span class="material-symbols-outlined">lock</span>
                        <input type="password" name="cpassword" required>
                        <label>Confirm Password</label>
                    </div>
                    <button type="submit" class="btn" name="submit">Register</button>
                    <div class="login-reg">
                        <p>Already have an Account?
                            <a href="../user/signIn.php" class="reg-link">Log in</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>