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
  <title>Contact - UniRoute</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <style>
    body {
      background-color: #f8f9fa;
    }
    .header {
      background: linear-gradient(to right, #007bff, #28a745);
      color: white;
      padding: 20px;
      text-align: center;
      font-size: 28px;
      font-weight: bold;
    }
    .card h5 {
      color: #343a40;
    }
    .go-back-btn {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
    }
    .go-back-btn:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>

<!-- Header -->
<?php include '../common/header.php'; ?>

<!-- Main Container -->
<div class="container my-5">
  <div class="row g-4">
    
    <!-- Bus List -->
    <div class="col-lg-6">
      <div class="card p-4">
        <h5 class="section-title">Bus List</h5>
        <div class="mb-3">
          <input type="text" id="routeFilter" class="form-control" placeholder="Type Route ID to filter..." />
        </div>
        <div id="bus-list">Loading buses...</div>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="col-lg-6">
      <div class="card p-4">
        <h5 class="section-title">Send a Message</h5>

        <!-- Success Alert -->
        <div id="messageAlert" class="alert alert-success d-none" role="alert"></div>

        <form id="messageForm">
          <div class="mb-3">
            <label class="form-label">Your Name</label>
            <input type="text" name="sender_name" class="form-control" readonly required />
          </div>
          <div class="mb-3">
            <label class="form-label">Bus ID</label>
            <input type="text" name="bus_id" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Send Message</button>
            <a href="../home/home.php" class="go-back-btn">Go Back</a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let allBuses = [];

  function loadBuses() {
    fetch("get_bus_contacts.php")
      .then(res => res.json())
      .then(data => {
        allBuses = data;
        displayBuses(allBuses);
      })
      .catch(err => {
        document.getElementById("bus-list").innerHTML = "<p class='text-danger'>Failed to load buses.</p>";
        console.error(err);
      });
  }

  function displayBuses(buses) {
    const list = document.getElementById("bus-list");
    list.innerHTML = "";

    if (buses.length === 0) {
      list.innerHTML = "<p>No buses found for this route.</p>";
      return;
    }

    buses.forEach(bus => {
      const box = document.createElement("div");
      box.className = "border rounded p-3 mb-3 bg-light";
      box.innerHTML = `
        <p><strong>Bus No:</strong> ${bus.bus_id}</p>
        <p><strong>Route ID:</strong> ${bus.route_id}</p>
        <p><strong>Status:</strong> ${bus.status}</p>
        <p><strong>Call:</strong> <a href="tel:${bus.contact_number}">${bus.contact_number}</a></p>
      `;
      list.appendChild(box);
    });
  }

  document.getElementById("routeFilter").addEventListener("input", function () {
    const keyword = this.value.trim().toLowerCase();
    const filtered = keyword === "" 
      ? allBuses 
      : allBuses.filter(bus => bus.route_id.toLowerCase().includes(keyword));
    displayBuses(filtered);
  });

  function fetchUserName() {
    fetch("get_logged_user.php")
      .then(res => res.text())
      .then(name => {
        document.querySelector("input[name='sender_name']").value = name || "Guest";
      })
      .catch(() => {
        document.querySelector("input[name='sender_name']").value = "Guest";
      });
  }

  function showAlert(message) {
    const alertBox = document.getElementById("messageAlert");
    alertBox.textContent = message;
    alertBox.classList.remove("d-none");

    setTimeout(() => {
      alertBox.classList.add("d-none");
    }, 4000);
  }

  document.getElementById("messageForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("save_message.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.text())
      .then(response => {
        this.reset();
        fetchUserName(); // Restore readonly name field
        showAlert(response);
      })
      .catch(error => {
        alert("Message not sent.");
        console.error(error);
      });
  });

  loadBuses();
  fetchUserName();
</script>

<?php include '../common/footer.html'; ?>
</body>
</html>
