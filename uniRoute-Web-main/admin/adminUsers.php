<?php
session_start();
include 'db.php';

// Handle new admin user creation button
if (isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['new_username']);
    $email = mysqli_real_escape_string($conn, $_POST['new_email']);
    $contactNum = mysqli_real_escape_string($conn, $_POST['new_contactNum']);
    $isAdmin = 1;
    $password_raw = $_POST['new_password'];
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    $insert_sql = "INSERT INTO user (username, email, contactNum, isAdmin, password) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssis", $username, $email, $contactNum, $isAdmin, $password);

    if ($insert_stmt->execute()) {
        $message = "Admin user added successfully!";
    } else {
        $message = "Failed to add admin user: " . $conn->error;
    }
}


// Handle user deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
// Fetch and delete user image if exists
    $image_sql = "SELECT image FROM user WHERE id = ?";
    $image_stmt = $conn->prepare($image_sql);
    $image_stmt->bind_param("i", $delete_id);
    $image_stmt->execute();
    $image_result = $image_stmt->get_result();
    $image_row = $image_result->fetch_assoc();
    
    if ($image_row['image'] && file_exists('../uploaded_img/' . $image_row['image'])) {
        unlink('../uploaded_img/' . $image_row['image']);
    }

// Delete user
    $delete_sql = "DELETE FROM user WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        $message = "User deleted successfully!";
    } else {
        $message = "Failed to delete user: " . $conn->error;
    }
}

// user update part
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contactNum = mysqli_real_escape_string($conn, $_POST['contactNum']);

    $update_sql = "UPDATE user SET username = ?, email = ?, contactNum = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $username, $email, $contactNum, $user_id);
    
    if ($update_stmt->execute()) {
        $message = "User updated successfully!";
    } else {
        $message = "Failed to update user: " . $conn->error;
    }
}

            // Fetch all users
$sql = "SELECT id, username, email, contactNum, isAdmin, image FROM user";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - UniRoute Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .user-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .page-title {
            color: #0d6efd;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
<?php include '../admin/adminLayout/header.php'; ?>
    <!-- Main container -->
    <div class="container mt-5 pt-5">
        <h2 class="page-title">Manage Users</h2>
        <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            + Add New Admin
        </button>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
                    <!-- Add Admin User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add Admin User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_username" class="form-label">Username</label>
                        <input type="text" name="new_username" id="new_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_email" class="form-label">Email</label>
                        <input type="email" name="new_email" id="new_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_contactNum" class="form-label">Contact Number</label>
                        <input type="text" name="new_contactNum" id="new_contactNum" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_user" class="btn btn-success">Add Admin User</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Admin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']); ?></td>
                            <td>
                                <img src="../<?= $row['image'] ? 'uploaded_img/' . htmlspecialchars($row['image']) : 'image/default-avatar.png'; ?>" class="user-img" alt="User">
                            </td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= htmlspecialchars($row['contactNum']); ?></td>
                            <td><?= $row['isAdmin'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <button class="btn btn-sm btn-success me-1"
                                    onclick="openUpdateModal(<?= $row['id']; ?>, '<?= htmlspecialchars($row['username']); ?>', '<?= htmlspecialchars($row['email']); ?>', '<?= htmlspecialchars($row['contactNum']); ?>')">
                                    Update
                                </button>
                                <a href="?delete_id=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this user?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

                <!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="user_id" id="modalUserId">
                <div class="mb-3">
                    <label for="modalUsername" class="form-label">Username</label>
                    <input type="text" name="username" id="modalUsername" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="modalEmail" class="form-label">Email</label>
                    <input type="email" name="email" id="modalEmail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="modalContactNum" class="form-label">Contact Number</label>
                    <input type="text" name="contactNum" id="modalContactNum" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="update_user" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

                <!-- Bootstrap Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let updateModal = new bootstrap.Modal(document.getElementById('updateModal'));

    function openUpdateModal(id, username, email, contactNum) {
        document.getElementById('modalUserId').value = id;
        document.getElementById('modalUsername').value = username;
        document.getElementById('modalEmail').value = email;
        document.getElementById('modalContactNum').value = contactNum;
        updateModal.show();
    }
</script>

</body>
</html>
