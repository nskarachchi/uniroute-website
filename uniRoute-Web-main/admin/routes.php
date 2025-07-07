<?php
session_start();
include 'db.php';

// Add new route
if (isset($_POST['add'])) {
    $route = $_POST['route'];
    $departure = $_POST['departure'];
    $conn->query("INSERT INTO route (route, depature) VALUES ('$route', '$departure')");
}

// Assign bus to route
if (isset($_POST['assign_bus'])) {
    $bus_id = $_POST['bus_id'];
    $route_id = $_POST['route_id'];
    $conn->query("UPDATE buses SET route_id = '$route_id' WHERE bus_id = '$bus_id'");
}

// Delete route
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM route WHERE route_id=$id");
}

// Fetch routes
$routes = $conn->query("SELECT * FROM route ORDER BY route_id DESC");

// Fetch all buses
$all_buses = $conn->query("SELECT bus_id FROM buses ORDER BY bus_id ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Routes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../admin/adminLayout/header.php'; ?>

<div class="container" style="margin-top: 80px;">
    <h2 class="mb-4 text-center">Route Management</h2>

    <!-- Add new route Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">Add New Route</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="route" class="form-control" placeholder="Route" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="departure" class="form-control" placeholder="Departure" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add" class="btn btn-success w-100">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Route Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">All Routes</div>
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Route</th>
                        <th>Departure</th>
                        <th>Assign Bus</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $routes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['route_id'] ?></td>
                            <td><?= htmlspecialchars($row['route']) ?></td>
                            <td><?= htmlspecialchars($row['depature']) ?></td>
                            <td>
                                <form method="POST" class="d-flex">
                                    <input type="hidden" name="route_id" value="<?= $row['route_id'] ?>">
                                    <select name="bus_id" class="form-select me-2" required>
                                        <option value="" disabled selected>Select Bus</option>
                                        <?php
                                        // Fetch available buses for each row to get fresh list
                                        $buses = $conn->query("SELECT bus_id FROM buses WHERE route_id IS NULL OR route_id = {$row['route_id']}");
                                        while ($bus = $buses->fetch_assoc()):
                                        ?>
                                            <option value="<?= $bus['bus_id'] ?>"><?= $bus['bus_id'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <button type="submit" name="assign_bus" class="btn btn-sm btn-primary">Assign</button>
                                </form>
                            </td>
                            <td>
                                <a href="?delete=<?= $row['route_id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this route?')">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($routes->num_rows == 0): ?>
                        <tr><td colspan="5" class="text-center text-muted">No routes found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
