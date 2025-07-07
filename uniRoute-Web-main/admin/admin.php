<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .admin-header {
            background: linear-gradient(to right, #007bff, #28a745);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .admin-header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .admin-header nav {
            display: flex;
            gap: 20px;
        }

        .admin-header nav a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .admin-header nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .admin-header .logout-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .admin-header .logout-btn:hover {
            background-color: #c9302c;
        }

        .hamburger {
            display: none;
            font-size: 24px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }

    </style>
</head>
<body class="bg-gray-100">
    <?php include '../admin/adminLayout/header.php'; ?>

    <div class="container mx-auto p-6">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">🚌 Bus Management Dashboard</h2>

        <!-- Display Existing Buses -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <h3 class="text-xl font-semibold mb-4">🚍 Existing Buses</h3>
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="p-2">Bus ID</th>
                        <th class="p-2">Latitude</th>
                        <th class="p-2">Longitude</th>
                        <th class="p-2">Contact Number</th>
                        <th class="p-2">Driver Email Address</th>
                        <th class="p-2">Status</th>
                    </tr>
                </thead>
                <tbody id="busList" class="text-center">
                    <!-- Bus Data Will Load Here -->
                </tbody>
            </table>
        </div>

        <!-- Add New Bus -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-6">
            <h3 class="text-xl font-semibold mb-4">➕ Add New Bus</h3>
            <form id="addBusForm" class="space-y-3">
                <input type="text" id="busId" placeholder="Bus ID" required class="w-full p-2 border rounded">
                <input type="text" id="latitude" placeholder="Latitude" required class="w-full p-2 border rounded">
                <input type="text" id="longitude" placeholder="Longitude" required class="w-full p-2 border rounded">
                <input type="text" id="contact_number" placeholder="Contact Number" required class="w-full p-2 border rounded">
                <input type="email" id="email" placeholder="email Address" required class="w-full p-2 border rounded">
                <select id="status" class="w-full p-2 border rounded">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="maintenance">Under Maintenance</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Bus</button>
            </form>
        </div>

        <!-- Update Bus -->
<div class="bg-white shadow-md rounded-lg p-4">
    <h3 class="text-xl font-semibold mb-4">🔄 Update Bus Location & Status</h3>
    <form id="updateBusForm" class="space-y-3">
        <input type="text" id="updateBusId" placeholder="Bus ID" required class="w-full p-2 border rounded">
        <input type="text" id="updateLatitude" placeholder="Latitude" required class="w-full p-2 border rounded">
        <input type="text" id="updateLongitude" placeholder="Longitude" required class="w-full p-2 border rounded">
        <input type="text" id="updateContactNumber" placeholder="Contact Number" required class="w-full p-2 border rounded">
        <input type="email" id="Updateemail" placeholder="email Address" required class="w-full p-2 border rounded">
        <select id="updateStatus" class="w-full p-2 border rounded">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="maintenance">Under Maintenance</option>
        </select>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Update Bus</button>
    </form>
</div>

    </div>

    

    <script src="admin.js"></script>
</body>
</html>
