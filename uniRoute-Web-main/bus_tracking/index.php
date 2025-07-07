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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Bus Tracking - UniRoute</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #eef2f7;
      color: #333;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background: linear-gradient(to right, #3498db, #2c3e50);
      color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    }

    header h2 {
      margin: 0;
    }

    .btn {
      background-color: #ffffff;
      color: #3498db;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      margin-left: 0.5rem;
      transition: all 0.3s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
      background-color: #3498db;
      color: #fff;
    }

    #map {
      height: 700px;
      width: 90%;
      margin: 20px auto;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      display: none; /* Hidden until a route is selected */
    }

    .bus-info {
      width: 90%;
      margin: 20px auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      display: none; /* Hidden until a route is selected */
    }

    .bus-box {
      background: #fff;
      padding: 16px;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      position: relative;
      overflow: hidden;
      border-left: 6px solid #2980b9;
    }

    .bus-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .status {
      display: inline-block;
      padding: 4px 10px;
      font-size: 0.9rem;
      border-radius: 12px;
      font-weight: bold;
    }

    .status.active {
      background: #2ecc71;
      color: white;
    }

    .status.inactive {
      background: #e74c3c;
      color: white;
    }

    .status.other {
      background: #f39c12;
      color: white;
    }

    .route-selection {
      width: 100%;
      margin: 80px auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      text-align: center;
    }

    .route-selection h3 {
      margin-bottom: 20px;
      color: #333;
    }

    .route-selection select {
      width: 100%;
      max-width: 300px;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 20px;
      cursor: pointer;
    }

    .route-selection button {
      background-color: #3498db;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .route-selection button:hover {
      background-color: #2980b9;
    }
    
    .reserve-btn {
      background-color: #27ae60;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 10px;
      width: 100%;
    }
    
    .reserve-btn:hover {
      background-color: #219653;
    }
    
    .reserve-btn.inactive {
      background-color: #95a5a6;
      cursor: not-allowed;
    }

    
    .header {
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

        .header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .header nav {
            display: flex;
            gap: 20px;
        }

        .header nav a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .header nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header .user-info span {
            font-size: 16px;
        }

        .header .logout-btn {
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

        .header .logout-btn:hover {
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
<body>
<?php include '../common/header.php'; ?>


  <!-- Route Selection UI -->
  <div class="route-selection">
    <h3>Select a Route</h3>
    <select id="routeSelect">
      <option value="">-- Select a Route --</option>
    </select>
    <button onclick="loadBuses()">Show Availabe Buses</button>
  </div>

  
  <div class="bus-info" id="bus-container"></div>
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    let map;

    // Redirect to reservation page with bus ID
    function reserveSeat(busId) {
      window.location.href = `../seat_reservation/index.php?bus_id=${busId}`;
    }

    // Load routes into the dropdown
    document.addEventListener("DOMContentLoaded", function () {
      fetch("../bus_tracking/routes.php")
        .then(response => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then(data => {
          const routeSelect = document.getElementById("routeSelect");
          if (data.error) {
            routeSelect.innerHTML = `<option value="">Error: ${data.error}</option>`;
            return;
          }
          data.forEach(route => {
            const option = document.createElement("option");
            option.value = route.route_id;
            option.textContent = `Route ${route.route_id} - ${route.route}`;
            routeSelect.appendChild(option);
          });
        })
        .catch(error => {
          console.error("Error loading routes:", error);
          document.getElementById("routeSelect").innerHTML = "<option value=''>Failed to load routes</option>";
        });
    });

    // Load buses for the selected route
    function loadBuses() {
      const routeId = document.getElementById("routeSelect").value;
      const mapDiv = document.getElementById("map");
      const busContainer = document.getElementById("bus-container");

      if (!routeId) {
        alert("Please select a route first.");
        return;
      }

      
      // Show map and bus info BEFORE initializing
      mapDiv.style.display = "block";
      busContainer.style.display = "grid";
      busContainer.innerHTML = "Loading buses...";

      

      // Initialize map if not already initialized
      if (!map) {
        map = L.map("map").setView([6.9271, 79.8612], 10);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution: '© OpenStreetMap contributors'
        }).addTo(map);
      }

      // Show map and bus info
      mapDiv.style.display = "block";
      busContainer.style.display = "grid";
      busContainer.innerHTML = "Loading buses...";

      fetch(`get_buses.php?route_id=${routeId}`)
        .then(response => response.json())
        .then(data => {
          busContainer.innerHTML = "";
          map.eachLayer(layer => {
            if (layer instanceof L.Marker) {
              map.removeLayer(layer);
            }
          });

          if (data.error) {
            busContainer.innerHTML = `<p>${data.error}</p>`;
            return;
          }

          if (data.length === 0) {
            busContainer.innerHTML = "<p>No buses found for this route.</p>";
            return;
          }

          data.forEach(bus => {
            if (!bus.latitude || !bus.longitude) {
              console.warn(`Bus ${bus.bus_id} has no location data.`);
              return;
            }

            // Add marker to map
            L.marker([bus.latitude, bus.longitude])
              .addTo(map)
              .bindPopup(`<b>Bus ${bus.bus_id}</b><br>Status: ${bus.status}`);

            // Determine status color
            let statusClass = "other";
            if (bus.status.toLowerCase() === "active") statusClass = "active";
            else if (bus.status.toLowerCase() === "inactive") statusClass = "inactive";

            // Create bus card
            const busBox = document.createElement("div");
            busBox.classList.add("bus-box");
            busBox.innerHTML = `
              <h3>Bus ${bus.bus_id}</h3>
              <p><span class="status ${statusClass}">${bus.status}</span></p>
              <p><strong>Contact:</strong> ${bus.contact_number}</p>
              <p><strong>Location:</strong> (${bus.latitude}, ${bus.longitude})</p>
            `;
            
            // Add reservation button
            const reserveBtn = document.createElement("button");
            reserveBtn.classList.add("reserve-btn");
            
            // Only allow reservations for active buses
            if (bus.status.toLowerCase() === "active") {
              reserveBtn.textContent = "Reserve a Seat";
              reserveBtn.addEventListener("click", () => reserveSeat(bus.bus_id));
            } else {
              reserveBtn.textContent = "Not Available";
              reserveBtn.classList.add("inactive");
              reserveBtn.disabled = true;
            }
            
            busBox.appendChild(reserveBtn);
            busContainer.appendChild(busBox);
          });

          // Adjust map view to fit all markers
          const markers = data.filter(bus => bus.latitude && bus.longitude)
            .map(bus => [bus.latitude, bus.longitude]);
          if (markers.length > 0) {
            map.fitBounds(markers);
          }
        })
        .catch(error => {
          console.error("Error loading buses:", error);
          busContainer.innerHTML = "<p>Failed to load buses.</p>";
        });
    }
  </script>
      <?php include '../common/footer.html'; ?>
</body>
</html>