<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    header("Location: ../user/signIn.php");
    exit();
}

// Handle Add Route
if (isset($_POST['add_route'])) {
    $route_id = mysqli_real_escape_string($conn, $_POST['route_id']);
    $route = mysqli_real_escape_string($conn, $_POST['route']);
    $departure = mysqli_real_escape_string($conn, $_POST['departure']);

    // Check if route_id already exists
    $check_sql = "SELECT * FROM routes WHERE route_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $route_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Route ID already exists!');</script>";
    } else {
        // Insert new route
        $insert_sql = "INSERT INTO routes (route_id, route, departure) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sss", $route_id, $route, $departure);

        if ($insert_stmt->execute()) {
            echo "<script>alert('Route added successfully!');</script>";
        } else {
            echo "<script>alert('Failed to add route: " . $conn->error . "');</script>";
        }
    }
}

// Handle Update Route
if (isset($_POST['update_route'])) {
    $route_id = mysqli_real_escape_string($conn, $_POST['update_route_id']);
    $route = mysqli_real_escape_string($conn, $_POST['update_route']);
    $departure = mysqli_real_escape_string($conn, $_POST['update_departure']);

    // Update route
    $update_sql = "UPDATE routes SET route = ?, departure = ? WHERE route_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sss", $route, $departure, $route_id);

    if ($update_stmt->execute()) {
        if ($update_stmt->affected_rows > 0) {
            echo "<script>alert('Route updated successfully!');</script>";
        } else {
            echo "<script>alert('No route found with that Route ID.');</script>";
        }
    } else {
        echo "<script>alert('Failed to update route: " . $conn->error . "');</script>";
    }
}

// Fetch all routes to display
$routes_sql = "SELECT * FROM routes";
$routes_result = $conn->query($routes_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Management Dashboard - UniRoute</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">🛤️ Route Management Dashboard</h2>

        <!-- Display Existing Routes -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <h3 class="text-xl font-semibold mb-4">📋 Existing Routes</h3>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="p-2">Route ID</th>
                        <th class="p-2">Route</th>
                        <th class="p-2">Departure</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="routeList" class="text-center">
                    <?php if ($routes_result->num_rows > 0): ?>
                        <?php while ($row = $routes_result->fetch_assoc()): ?>
                            <tr class="border-b">
                                <td class="p-2"><?php echo htmlspecialchars($row['route_id']); ?></td>
                                <td class="p-2"><?php echo htmlspecialchars($row['route']); ?></td>
                                <td class="p-2"><?php echo htmlspecialchars($row['departure']); ?></td>
                                <td class="p-2">
                                    <button onclick="populateUpdateForm('<?php echo $row['route_id']; ?>', '<?php echo $row['route']; ?>', '<?php echo $row['departure']; ?>')" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Edit</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="p-2 text-gray-500">No routes found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Add New Route -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <h3 class="text-xl font-semibold mb-4">➕ Add New Route</h3>
            <form id="addRouteForm" method="POST" action="" class="space-y-3">
                <input type="text" id="routeId" name="route_id" placeholder="Route ID (e.g., R001)" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" id="route" name="route" placeholder="Route (e.g., Downtown to Airport)" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" id="departure" name="departure" placeholder="Departure Time (e.g., 08:00 AM)" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" name="add_route" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200">Add Route</button>
            </form>
        </div>

        <!-- Update Route -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-xl font-semibold mb-4">🔄 Update Route</h3>
            <form id="updateRouteForm" method="POST" action="" class="space-y-3">
                <input type="text" id="updateRouteId" name="update_route_id" placeholder="Route ID" required readonly class="w-full p-2 border rounded bg-gray-100 cursor-not-allowed">
                <input type="text" id="updateRoute" name="update_route" placeholder="Route" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                <input type="text" id="updateDeparture" name="update_departure" placeholder="Departure Time" required class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500">
                <button type="submit" name="update_route" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition duration-200">Update Route</button>
            </form>
        </div>
    </div>

    <script>
        // Function to populate the update form with route data
        function populateUpdateForm(routeId, route, departure) {
            document.getElementById('updateRouteId').value = routeId;
            document.getElementById('updateRoute').value = route;
            document.getElementById('updateDeparture').value = departure;
        }
    </script>
</body>
</html>