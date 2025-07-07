<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../user/signIn.php");
    exit();
}

$loggedInEmail = $_SESSION['email'];

// Get user's name
$userName = "User";
$sql = "SELECT username FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $userName = $row['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0&icon_names=account_circle" />
    <link rel="stylesheet" href="../home/home.css">
    <script>
        function navigateTo(page) {
            alert('Navigating to ' + page);
        }
    </script>
</head>
<body>
<?php include '../common/header.php'; ?>


    <div class="container">
        <h2>Available Bus Routes</h2>
        <div class="routes" id="routes-container">
            <p>Loading routes...</p>
        </div>
        
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('../home/routes.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const routesContainer = document.getElementById('routes-container');
                    routesContainer.innerHTML = '';

                    if (data.error) {
                        routesContainer.innerHTML = `<p>Error: ${data.error}</p>`;
                        return;
                    }

                    if (data.length === 0) {
                        routesContainer.innerHTML = '<p>No routes available.</p>';
                        return;
                    }

                    data.forEach(route => {
                        const routeBox = document.createElement('div');
                        routeBox.className = 'route-box';
                        routeBox.innerHTML = `
                            <img src="../image/route.avif" alt="Route ${route.route_id}">
                            <h3>Route ${route.route_id}</h3>
                            <p>${route.route}</p>
                            <p><strong>Departure:</strong> ${route.departure}</p>
                            <button onclick="goToRoute(${route.route_id})">Find Bus</button>
                        `;
                        routesContainer.appendChild(routeBox);
                    });
                })
                .catch(error => {
                    console.error("Error loading routes:", error);
                    document.getElementById('routes-container').innerHTML = `<p>Unable to load routes. Please try again later.</p>`;
                });
        });

        function goToRoute(routeId) {
            window.location.href = `../bus_tracking/index.php?route_id=${routeId}`;
        }
        </script>
    </div>

    <div class="features">
        <div class="feature-box" onclick="navigateTo('Real-Time Tracking')">
            <img src="../image/real time.jpg" alt="Real-Time Tracking">
            <h3>Real-Time Tracking</h3>
            <p>Get live updates on shuttle locations, reducing wait times.</p>
        </div>
        <div class="feature-box">
            <a href="../home/timeTable.php" style="text-decoration: none; color: black;">
            <img src="../image/schedule.jpg" alt="Smart Scheduling">
            <h3>Smart Scheduling</h3>
            <p>Optimized routes based on real-time traffic and demand.</p></a>
        </div>
        <div class="feature-box" onclick="navigateTo('Easy Seat Booking')">
            <img src="../image/ticket.png" alt="Easy Seat Booking">
            <h3>Easy Seat Booking</h3>
            <p>Reserve your seat in advance and travel hassle-free.</p>
        </div>
    </div>

    <div class="map-container">
        <h3 style="text-align: center;">Live Bus Locations</h3>
        <div id="map" style="height: 500px;"></div>
        
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var map = L.map("map").setView([6.9271, 79.8446], 11);

                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: '© OpenStreetMap',
                }).addTo(map);

                fetch(`get_all_buses.php`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            console.warn("No buses found.");
                            return;
                        }

                        data.forEach(bus => {
                            if (!bus.latitude || !bus.longitude) {
                                console.warn(`Bus ${bus.bus_id} has no location data.`);
                                return;
                            }

                            L.marker([bus.latitude, bus.longitude])
                                .addTo(map)
                                .bindPopup(`<b>Bus ${bus.bus_id}</b><br>Status: ${bus.status}`);
                        });
                    })
                    .catch(error => {
                        console.error("Error loading buses:", error);
                    });
            });
        </script>
    </div>

    <?php include '../common/footer.html'; ?>

    <script>
        // Toggle mobile menu
        const hamburger = document.querySelector('.hamburger');
        const nav = document.querySelector('nav');
        hamburger.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
    </script>
</body>
</html>