<?php
// Check if form was accessed correctly via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<h2 style='color: red; text-align: center;'>❌ Please select a seat and enter your details first.</h2>";
    exit;
}

// Get customer data
$seat_id = $_POST['seat_id'] ?? null;
$bus_id = $_POST['bus_id'] ?? null;
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';

// Validate data presence
if (!$seat_id || empty($name) || empty($email) || empty($phone)) {
    echo "<h2 style='color: red; text-align: center;'>⚠️ Missing customer details. Please go back and try again.</h2>";
    exit;
}

// Connect to database
$conn = new mysqli("localhost", "root", "pass", "uniroot");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if seat exists and is available
$seat_id = (int)$seat_id;
$result = $conn->query("SELECT * FROM seats WHERE seat_id = $seat_id");

if ($result->num_rows === 0) {
    echo "<h2 style='color: red; text-align: center;'>❌ Seat not found. Please choose a valid seat.</h2>";
    exit;
}

$seat = $result->fetch_assoc();
if ($seat['is_reserved']) {
    echo "<h2 style='color: red; text-align: center;'>❌ Seat " . htmlspecialchars($seat['seat_number']) . " is already reserved. Please choose another one.</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f5f6fa;
      padding: 20px;
    }

    .container {
      max-width: 500px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #2f3640;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin: 15px 0 5px;
      font-weight: 600;
    }

    input {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }

    .btn {
      margin-top: 20px;
      padding: 14px;
      width: 100%;
      background-color: #273c75;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn:hover {
      background-color: #40739e;
    }

    .seat-info {
      text-align: center;
      margin-bottom: 20px;
      font-size: 18px;
      color: #44bd32;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Checkout</h2>
    <p class="seat-info">Reserving Seat: <strong><?php echo htmlspecialchars($seat['seat_number']); ?></strong></p>

    <form action="confirm.php" method="POST">
      <!-- Hidden customer fields -->
      <input type="hidden" name="seat_id" value="<?php echo htmlspecialchars($seat_id); ?>">
      <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus_id); ?>">
      <input type="hidden" name="name" value="<?php echo htmlspecialchars($name); ?>">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
      <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">

      <label>Name on Card</label>
      <input type="text" placeholder="John Doe" required>

      <label>Card Number</label>
      <input type="text" placeholder="1234 5678 9012 3456" required>

      <label>Expiry Date</label>
      <input type="month" required>

      <label>CVV</label>
      <input type="text" placeholder="123" required>

      <button type="submit" class="btn">Confirm Payment</button>
    </form>
  </div>
</body>
</html>
<?php
$conn->close();
?>