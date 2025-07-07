<?php

session_start();

$conn = new mysqli("localhost", "root", "pass", "uniroot");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email'])) {
    header("Location: ../user/signIn.php");
    exit();
}

$loggedInEmail = $_SESSION['email'];

// Fetch the logged-in user's name
$userName = "Guest";
$sql = "SELECT username FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $userName = $row['username'];
}
$stmt->close();

$seat_id = isset($_POST['seat_id']) ? (int)$_POST['seat_id'] : null;

if (!$seat_id) {
    echo "<h3 style='color:red; text-align:center;'>No seat selected.</h3>";
    exit;
}

// Fetch seat info
$result = $conn->query("SELECT seat_number, bus_id FROM seats WHERE seat_id = $seat_id");
if ($result && $result->num_rows > 0) {
  $seat = $result->fetch_assoc();
  $bus_id = $seat['bus_id'];
} else {
  echo "<h3 style='color:red; text-align:center;'>Selected seat not found.</h3>";
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Details - UniRoute</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f5f6fa;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      max-width: 500px;
      width: 100%;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin: 12px 0 5px;
      font-weight: 600;
    }

    input {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }

    .btn {
      margin-top: 20px;
      width: 100%;
      padding: 14px;
      background-color: #273c75;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #40739e;
    }

    .seat-info {
      text-align: center;
      margin-bottom: 15px;
      font-size: 18px;
      font-weight: 600;
      color: #2f3640;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Customer Details</h2>
    <div class="seat-info">Selected Seat: <strong><?php echo htmlspecialchars($seat['seat_number']); ?></strong></div>

    <form action="payment.php" method="POST">
      <input type="hidden" name="seat_id" value="<?php echo $seat_id; ?>">
      <input type="hidden" name="bus_id" value="<?php echo $bus_id; ?>">

      <label for="name">Full Name</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($userName); ?>" readonly>

      <label for="email">Email</label>
      <input type="email" name="email" required>

      <label for="phone">Phone Number</label>
      <input type="text" name="phone" required>

      <button type="submit" class="btn">Continue to Payment</button>
    </form>
  </div>
</body>
</html>
<?php
$conn->close();
?>