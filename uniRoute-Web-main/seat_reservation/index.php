<?php
session_start();

$conn = new mysqli("localhost", "root", "pass", "uniroot");

$bus_id = isset($_GET['bus_id']) ? (int)$_GET['bus_id'] : 0;
$seats = $conn->query("SELECT * FROM seats WHERE bus_id = $bus_id ORDER BY seat_number");


if (!$bus_id) {
  echo "<h3 style='color:red; text-align:center;'>No bus selected.</h3>";
  exit;
}

$seats = $conn->query("SELECT * FROM seats WHERE bus_id = $bus_id ORDER BY seat_number");

?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bus Seat Reservation - UniRoute</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  body {
    font-family: 'Inter', sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 20px;
  }

  .container {
    max-width: 960px;
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin: auto;
    margin-top: 60px;
  }

  h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #2f3640;
  }

  .bus-layout {
    background: #f0f3f5;
    border-radius: 15px;
    padding: 30px 20px;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    position: relative;
  }

  .seat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    width: 45%;
  }

  .seat {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    position: relative;
    cursor: pointer;
    transition: transform 0.2s ease;
  }

  .seat:hover {
    transform: scale(1.05);
  }

  .seat input {
    position: absolute;
    opacity: 0;
  }

  .seat input:checked + span {
    background-color: #2980b9 !important;
  }

  .seat span {
    width: 100%;
    height: 100%;
    border-radius: 10px;
    text-align: center;
    line-height: 60px;
    background-color: #27ae60;
    display: inline-block;
  }

  .seat.reserved span {
    background-color: #c0392b !important;
    cursor: not-allowed;
  }

  .aisle {
    position: absolute;
    left: 50%;
    top: 20px;
    bottom: 20px;
    width: 12px;
    background: repeating-linear-gradient(
      0deg,
      #bdc3c7 0px,
      #bdc3c7 10px,
      transparent 10px,
      transparent 20px
    );
    transform: translateX(-50%);
    z-index: 1;
  }

  .submit-btn {
    margin-top: 30px;
    width: 100%;
    padding: 14px;
    font-size: 16px;
    background: #273c75;
    color: white;
    border: none;
    border-radius: 8px;
    transition: background 0.3s ease;
  }

  .submit-btn:hover {
    background: #40739e;
  }

  .bus-front {
    text-align: center;
    margin-bottom: 20px;
    font-size: 18px;
    color: #7f8c8d;
  }

  .bus-front::before {
    content: "🚍 Front";
    display: block;
    font-size: 24px;
  }

  .legend {
    position: fixed;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 15px 20px;
    z-index: 999;
  }

  .legend .legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
  }

  .legend .color-box {
    width: 20px;
    height: 20px;
    border-radius: 4px;
  }

  .available { background-color: #27ae60; }
  .reserved { background-color: #c0392b; }
  .selected { background-color: #2980b9; }

  @media (max-width: 768px) {
    .bus-layout {
      flex-direction: column;
      align-items: center;
    }

    .seat-grid {
      width: 100%;
    }

    .aisle {
      display: none;
    }

    .legend {
      top: auto;
      bottom: 15px;
      right: 15px;
    }
  }
</style>

</head>
<body>
<div class="legend">
  <div class="legend-item"><div class="color-box available"></div>Available</div>
  <div class="legend-item"><div class="color-box reserved"></div>Booked</div>
  <div class="legend-item"><div class="color-box selected"></div>Selected</div>
</div>

  <div class="container">
    <h2>Select Your Seat</h2>
    <form action="reserve.php" method="POST">
      <input type="hidden" name="bus_id" value="<?php echo $bus_id; ?>">
      <div class="bus-front">Bus Entrance</div>
      <?php if ($seats->num_rows > 0): ?>
        <div class="bus-layout">
          <!-- Left Side Seats -->
          <div class="seat-grid">
            <?php 
            $seat_count = $seats->num_rows;
            $half_seats = ceil($seat_count / 2);
            $counter = 0;
            $seats->data_seek(0);
            while ($counter < $half_seats && ($row = $seats->fetch_assoc())): 
              $counter++;
            ?>
              <?php if (isset($row['is_reserved']) && $row['is_reserved']): ?>
                <div class="seat reserved"><span><?php echo $row['seat_number']; ?></span></div>
              <?php else: ?>
                <label class="seat">
                  <input type="radio" name="seat_id" value="<?php echo $row['seat_id']; ?>">
                  <span><?php echo $row['seat_number']; ?></span>
                </label>
              <?php endif; ?>
            <?php endwhile; ?>
          </div>

          <!-- Aisle -->
          <div class="aisle"></div>

          <!-- Right Side Seats -->
          <div class="seat-grid">
            <?php 
            $seats->data_seek($half_seats);
            while ($row = $seats->fetch_assoc()): 
            ?>
              <?php if (isset($row['is_reserved']) && $row['is_reserved']): ?>
                <div class="seat reserved"><span><?php echo $row['seat_number']; ?></span></div>
              <?php else: ?>
                <label class="seat">
                  <input type="radio" name="seat_id" value="<?php echo $row['seat_id']; ?>">
                  <span><?php echo $row['seat_number']; ?></span>
                </label>
              <?php endif; ?>
            <?php endwhile; ?>
          </div>
        </div>
        <button type="submit" class="submit-btn">Reserve & Continue to Payment</button>
      <?php else: ?>
        <p class="error-message">No seats available for this bus.</p>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>