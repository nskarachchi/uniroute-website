<?php
include 'connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: signIn.php");
    exit();
}

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the user's ID based on their email
$loggedInEmail = $_SESSION['email'];
$sql = "SELECT id FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_id = $row['id'];
} else {
    header("Location: signIn.php");
    exit();
}

// Initialize message array
$message = [];

// Handle account deletion
if (isset($_GET['delete_account']) && $_GET['delete_account'] == 'true') {
    $image_sql = "SELECT image FROM user WHERE id = ?";     // Delete user's profile image if it exists
    $image_stmt = $conn->prepare($image_sql);
    $image_stmt->bind_param("i", $user_id);
    $image_stmt->execute();
    $image_result = $image_stmt->get_result();
    $image_row = $image_result->fetch_assoc();
    
    if ($image_row['image'] && file_exists('../uploaded_img/' . $image_row['image'])) {
        unlink('../uploaded_img/' . $image_row['image']);
    }

    // Delete user from database
    $delete_sql = "DELETE FROM user WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $user_id);
    
    if ($delete_stmt->execute()) {
        session_destroy();
        header("Location: signIn.php?message=Account deleted successfully");
        exit();
    } else {
        $message[] = "Failed to delete account: " . $conn->error;
    }
}

// Handle image update
if (isset($_FILES['update_image']) && $_FILES['update_image']['size'] > 0) {
    $update_image = $_FILES['update_image']['name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_folder = '../uploaded_img/' . $update_image;

    // Debug: Check image details
    error_log("Image update: Name=$update_image, Size=$update_image_size");

    if ($update_image_size > 2000000) {
        $message[] = "Image is too large (max 2MB)!";
        error_log("Image too large: $update_image_size bytes");
    } else {
        // Check if upload directory exists, create if not
        if (!file_exists('../uploaded_img/')) {
            mkdir('../uploaded_img/', 0755, true);
            error_log("Created upload directory");
        }
        
        // Generate unique filename to prevent overwriting
        $file_extension = pathinfo($update_image, PATHINFO_EXTENSION);
        $new_filename = uniqid('profile_') . '.' . $file_extension;
        $update_image_folder = '../uploaded_img/' . $new_filename;
        
        // Delete old image if exists
        $old_image_sql = "SELECT image FROM user WHERE id = ?";
        $old_image_stmt = $conn->prepare($old_image_sql);
        $old_image_stmt->bind_param("i", $user_id);
        $old_image_stmt->execute();
        $old_image_result = $old_image_stmt->get_result();
        $old_image_row = $old_image_result->fetch_assoc();
        
        if ($old_image_row['image'] && file_exists('../uploaded_img/' . $old_image_row['image'])) {
            unlink('../uploaded_img/' . $old_image_row['image']);
            error_log("Deleted old image: " . $old_image_row['image']);
        }

        // Update image in database first
        $image_update_sql = "UPDATE user SET image = ? WHERE id = ?";
        $image_update_stmt = $conn->prepare($image_update_sql);
        $image_update_stmt->bind_param("si", $new_filename, $user_id);
        
        if ($image_update_stmt->execute()) {
            // Then try to move the uploaded file
            if (move_uploaded_file($update_image_tmp_name, $update_image_folder)) {
                $message[] = "Image updated successfully!";
                error_log("Image updated successfully: $new_filename");
            } else {
                $message[] = "Failed to upload image to server! Error: " . error_get_last()['message'];
                error_log("Failed to move uploaded file. Error: " . error_get_last()['message']);
                
                // Revert the database update since file upload failed
                $revert_sql = "UPDATE user SET image = ? WHERE id = ?";
                $revert_stmt = $conn->prepare($revert_sql);
                $revert_stmt->bind_param("si", $old_image_row['image'], $user_id);
                $revert_stmt->execute();
            }
        } else {
            $message[] = "Failed to update image in database: " . $conn->error;
            error_log("Failed to update image in database: " . $conn->error);
        }
    }
}

// Handle password change
if (!empty($_POST['current_password']) || !empty($_POST['new_password']) || !empty($_POST['confirm_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch existing hashed password from DB
    $password_sql = "SELECT password FROM user WHERE id = ?";
    $password_stmt = $conn->prepare($password_sql);
    $password_stmt->bind_param("i", $user_id);
    $password_stmt->execute();
    $password_result = $password_stmt->get_result();
    $password_row = $password_result->fetch_assoc();

    if (!password_verify($current_password, $password_row['password'])) {
        $message[] = "Current password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $message[] = "New passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $message[] = "New password must be at least 6 characters long.";
    } else {
        // Hash and update new password
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_pass_sql = "UPDATE user SET password = ? WHERE id = ?";
        $update_pass_stmt = $conn->prepare($update_pass_sql);
        $update_pass_stmt->bind_param("si", $new_hashed_password, $user_id);

        if ($update_pass_stmt->execute()) {
            $message[] = "Password updated successfully!";
        } else {
            $message[] = "Failed to update password: " . $conn->error;
        }
    }
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    error_log("Form submitted: update_profile");
    
    $update_name = mysqli_real_escape_string($conn, $_POST['username']);
    $update_email = mysqli_real_escape_string($conn, $_POST['email']);
    $contactNum = isset($_POST['contactNum']) ? mysqli_real_escape_string($conn, $_POST['contactNum']) : '';

    // Check if the new email is already in use by another user
    if ($update_email != $loggedInEmail) {
        $email_check_sql = "SELECT id FROM user WHERE email = ? AND id != ?";
        $email_check_stmt = $conn->prepare($email_check_sql);
        $email_check_stmt->bind_param("si", $update_email, $user_id);
        $email_check_stmt->execute();
        $email_check_result = $email_check_stmt->get_result();

        if ($email_check_result->num_rows > 0) {
            $message[] = "This email is already in use by another user!";
        } else {
            // Update username, email and contact number
            $update_sql = "UPDATE user SET username = ?, email = ?, contactNum = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssi", $update_name, $update_email, $contactNum, $user_id);
            
            if ($update_stmt->execute()) {
                // Update the session with new email
                $_SESSION['email'] = $update_email;
                $message[] = "Profile updated successfully!";
                error_log("Profile updated - Username: $update_name, Email: $update_email, Contact: $contactNum");
            } else {
                $message[] = "Failed to update profile: " . $conn->error;
                error_log("Failed to update profile: " . $conn->error);
            }
        }
    } else {
        // Just update the username and contact number if email didn't change
        $update_sql = "UPDATE user SET username = ?, contactNum = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $update_name, $contactNum, $user_id);
        
        if ($update_stmt->execute()) {
            $message[] = "Profile updated successfully!";
            error_log("Profile updated - Username: $update_name, Contact: $contactNum");
        } else {
            $message[] = "Failed to update profile: " . $conn->error;
            error_log("Failed to update profile: " . $conn->error);
        }
    }
    
    // Redirect to prevent form resubmission on page refresh
    if (count($message) > 0) {
        $_SESSION['update_messages'] = $message;
        header("Location: updateProfile.php");
        exit();
    }
}

// Fetch user data for display
$select_sql = "SELECT * FROM user WHERE id = ?";
$select_stmt = $conn->prepare($select_sql);
$select_stmt->bind_param("i", $user_id);
$select_stmt->execute();
$select_result = $select_stmt->get_result();
$fetch = $select_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="icon" href="../resources/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../user/updateProfile.css">
</head>
<body>
    <div class="update-profile">
        <h2>Update Your Profile</h2>

        <!-- Profile Picture -->
        <div class="profile-pic-container">
            <?php
            // Display profile picture
            if (isset($fetch['image']) && !empty($fetch['image']) && file_exists('../uploaded_img/' . $fetch['image'])) {
                echo '<img src="../uploaded_img/' . htmlspecialchars($fetch['image']) . '" alt="Profile Image" class="profile-pic">';
            } else {
                echo '<img src="../image/default-avatar.png" alt="Default Avatar" class="profile-pic">';
            }
            ?>
            <label for="update_image" class="profile-pic-label">
                <span class="material-symbols-outlined">edit</span>
            </label>
        </div>

        <!-- Messages -->
        <?php
        // Display messages from session if they exist
        if (isset($_SESSION['update_messages'])) {
            $message = $_SESSION['update_messages'];
            unset($_SESSION['update_messages']);
        }
        
        if (!empty($message)): ?>
            <div class="message-container">
                <?php foreach ($message as $msg): ?>
                    <div class="message <?php echo (strpos($msg, "successfully") !== false) ? 'success' : 'error'; ?>">
                        <?php echo htmlspecialchars($msg); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="" method="post" enctype="multipart/form-data" id="updateProfileForm" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($fetch['username']); ?>" required>
                <span class="material-symbols-outlined icon">person</span>
                <div class="invalid-feedback">Please enter a username.</div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($fetch['email']); ?>" required>
                <span class="material-symbols-outlined icon">email</span>
                <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>
            
            <div class="form-group">
                <label for="contactNum">Contact Number</label>
                <input type="text" id="contactNum" name="contactNum" value="<?php echo htmlspecialchars($fetch['contactNum']); ?>">
                <span class="material-symbols-outlined icon">phone</span>
                <div class="invalid-feedback">Please enter a valid phone number.</div>
            </div>

            <div class="form-group">
                <label for="update_image">Profile Picture (Max 2MB)</label>
                <input type="file" id="update_image" name="update_image" accept="image/jpg, image/jpeg, image/png" class="file-input">
                <span class="material-symbols-outlined icon">image</span>
                <div class="hint">Supported formats: JPG, JPEG, PNG</div>
            </div>

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password">
                <span class="material-symbols-outlined icon">lock</span>
                <div class="invalid-feedback">Please enter your current password.</div>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password">
                <span class="material-symbols-outlined icon">lock_reset</span>
                <div class="invalid-feedback">Password must be at least 6 characters long.</div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
                <span class="material-symbols-outlined icon">lock_reset</span>
                <div class="invalid-feedback">Passwords do not match.</div>
            </div>

            <div class="buttons">
                <button type="submit" name="update_profile" class="btn-save">Save Changes</button>
                <a href="../user/viewProfile.php" class="btn-cancel">Back</a>
                <button type="button" id="deleteAccount" class="btn-delete">Delete Account</button>
            </div>
        </form>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Delete Account</h3>
            <p>Are you sure you want to delete your account? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button id="cancelDelete" class="btn-cancel">Cancel</button>
                <button id="confirmDelete" class="btn-delete">Yes, Delete</button>
            </div>
        </div>
    </div>

    <script>
        // Form validation 
        document.getElementById('updateProfileForm').addEventListener('submit', function(event) {
            let isValid = true;
            
            // Email validation
            const emailInput = document.getElementById('email');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailPattern.test(emailInput.value)) {
                emailInput.classList.add('form-control-invalid');
                emailInput.nextElementSibling.nextElementSibling.style.display = 'block';
                isValid = false;
            } else {
                emailInput.classList.remove('form-control-invalid');
                emailInput.nextElementSibling.nextElementSibling.style.display = 'none';
            }
            
            // Username validation
            const usernameInput = document.getElementById('username');
            if (usernameInput.value.trim() === '') {
                usernameInput.classList.add('form-control-invalid');
                usernameInput.nextElementSibling.nextElementSibling.style.display = 'block';
                isValid = false;
            } else {
                usernameInput.classList.remove('form-control-invalid');
                usernameInput.nextElementSibling.nextElementSibling.style.display = 'none';
            }
            
            // Password validation if any of the password fields are filled
            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            
            if (currentPassword.value || newPassword.value || confirmPassword.value) {
                // Current password needs to be filled
                if (!currentPassword.value) {
                    currentPassword.classList.add('form-control-invalid');
                    currentPassword.nextElementSibling.nextElementSibling.style.display = 'block';
                    isValid = false;
                } else {
                    currentPassword.classList.remove('form-control-invalid');
                    currentPassword.nextElementSibling.nextElementSibling.style.display = 'none';
                }
                
                // New password needs to be at least 6 characters
                if (newPassword.value.length < 6) {
                    newPassword.classList.add('form-control-invalid');
                    newPassword.nextElementSibling.nextElementSibling.style.display = 'block';
                    isValid = false;
                } else {
                    newPassword.classList.remove('form-control-invalid');
                    newPassword.nextElementSibling.nextElementSibling.style.display = 'none';
                }
                
                // Confirm password needs to match new password
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.classList.add('form-control-invalid');
                    confirmPassword.nextElementSibling.nextElementSibling.style.display = 'block';
                    isValid = false;
                } else {
                    confirmPassword.classList.remove('form-control-invalid');
                    confirmPassword.nextElementSibling.nextElementSibling.style.display = 'none';
                }
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });

        // Delete account modal functionality
        const modal = document.getElementById('deleteModal');
        const btnDelete = document.getElementById('deleteAccount');
        const closeBtn = document.querySelector('.close');
        const cancelDelete = document.getElementById('cancelDelete');
        const confirmDelete = document.getElementById('confirmDelete');

        btnDelete.addEventListener('click', function() {
            modal.style.display = 'block';
        });

        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        cancelDelete.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        confirmDelete.addEventListener('click', function() {
            window.location.href = 'updateProfile.php?delete_account=true';
        });

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });

        // Preview image before upload
        document.getElementById('update_image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-pic').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>