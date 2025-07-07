<?php
session_start();
include 'connection.php';

// Fetch statistics for dashboard cards
$totalBookings = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$activeShuttles = $pdo->query("SELECT COUNT(DISTINCT bus_id) FROM customers")->fetchColumn();
$recentBookings = $pdo->query("SELECT COUNT(*) FROM customers WHERE reserved_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();

// Calculate percentage increase for bookings (assuming we track this - using placeholder for now)
$lastMonthBookings = $pdo->query("SELECT COUNT(*) FROM customers WHERE reserved_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
$bookingPercentage = $lastMonthBookings > 0 ? round(($recentBookings - $lastMonthBookings) / $lastMonthBookings * 100) : 0;

// Fetch recent bookings
$stmt = $pdo->query("SELECT id, name, phone, reserved_at, bus_id FROM customers ORDER BY reserved_at DESC LIMIT 5");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UniRoute Dashboard</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --dark: #212529;
      --light: #f8f9fa;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: #f5f7fa;
      color: var(--dark);
    }

    .main-content {
      width: 100%;
      margin-top: 80px;
    }

    .cards-section {
      padding: 25px;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      
    }

    .card {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 20px;
      transition: transform 0.3s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      
    }

    .card-icon {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: white;
    }

    .card-icon.blue { background-color: var(--primary); }
    .card-icon.green { background-color: var(--success); }
    .card-icon.orange { background-color: var(--warning); }
    .card-icon.pink { background-color: var(--danger); }

    .card-title {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 5px;
    }

    .card-value {
      font-size: 1.8rem;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .card-footer {
      font-size: 0.8rem;
      color: #6c757d;
    }

    .recent-bookings {
      padding: 0 25px 25px;
    }

    .section-title {
      font-size: 1.2rem;
      margin-bottom: 20px;
      color: var(--dark);
      display: flex;
      align-items: center;
    }

    .section-title i {
      margin-right: 10px;
      color: var(--primary);
    }

    .action-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .table-responsive {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 20px;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #e9ecef;
    }

    th {
      background-color: #f8f9fa;
      color: #495057;
      font-weight: 600;
    }

    tr:hover {
      background-color: #f8f9fa;
    }

    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 500;
      text-transform: uppercase;
    }

    .status.bus-1 {
      background-color: rgba(40, 167, 69, 0.1);
      color: #28a745;
    }

    .status.bus-2 {
      background-color: rgba(255, 193, 7, 0.1);
      color: #ffc107;
    }

    .status.bus-3 {
      background-color: rgba(13, 110, 253, 0.1);
      color: #0d6efd;
    }

    .status.default {
      background-color: rgba(108, 117, 125, 0.1);
      color: #6c757d;
    }

    .btn {
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 0.8rem;
      cursor: pointer;
      border: none;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background-color: var(--secondary);
    }

    .btn-sm {
      padding: 3px 8px;
      font-size: 0.75rem;
    }

    .text-success {
      color: #28a745;
    }

    .text-danger {
      color: #dc3545;
    }

    .view-all-btn {
      margin-left: auto;
      display: inline-block;
      color: var(--primary);
      text-decoration: none;
      font-size: 0.9rem;
      transition: all 0.3s;
    }

    .view-all-btn:hover {
      color: var(--secondary);
    }

    @media (max-width: 768px) {
      .cards-section {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      }
    }

    @media (max-width: 576px) {
      .cards-section {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
<?php include '../admin/adminLayout/header.php'; ?>

  <div class="main-content">
    <div class="cards-section">
      <div class="card">
        <div class="card-header">
          <div>
            <p class="card-title">Total Bookings</p>
            <h3 class="card-value"><?= number_format($totalBookings) ?></h3>
            <p class="card-footer">
              <?php if($bookingPercentage >= 0): ?>
                <i class="fas fa-arrow-up text-success"></i> <?= $bookingPercentage ?>% from last month
              <?php else: ?>
                <i class="fas fa-arrow-down text-danger"></i> <?= abs($bookingPercentage) ?>% from last month
              <?php endif; ?>
            </p>
          </div>
          <div class="card-icon blue"><i class="fas fa-calendar-check"></i></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div>
            <p class="card-title">Recent Bookings</p>
            <h3 class="card-value"><?= number_format($recentBookings) ?></h3>
            <p class="card-footer">In the last 30 days</p>
          </div>
          <div class="card-icon green"><i class="fas fa-ticket-alt"></i></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div>
            <p class="card-title">Active Shuttles</p>
            <h3 class="card-value"><?= $activeShuttles ?></h3>
            <p class="card-footer">Currently operating</p>
          </div>
          <div class="card-icon orange"><i class="fas fa-bus"></i></div>
        </div>
      </div>
    </div>

    <div class="recent-bookings">
      <div class="action-bar">
        <h3 class="section-title"><i class="fas fa-history"></i> Recent Bookings</h3>
        <a href="bookings.php" class="view-all-btn">View All Bookings <i class="fas fa-arrow-right"></i></a>
      </div>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Passenger Name</th>
              <th>Phone</th>
              <th>Reserved At</th>
              <th>Bus ID</th>
            </tr>
          </thead>
          <tbody>
            <?php if(count($bookings) > 0): ?>
              <?php foreach($bookings as $booking): ?>
                <tr>
                  <td>#<?= htmlspecialchars($booking['id']) ?></td>
                  <td><?= htmlspecialchars($booking['name']) ?></td>
                  <td><?= htmlspecialchars($booking['phone']) ?></td>
                  <td><?= date('Y-m-d H:i', strtotime($booking['reserved_at'])) ?></td>
                  <td>
                    <?php 
                      $busClass = 'default';
                      if($booking['bus_id'] == 1) $busClass = 'bus-1';
                      else if($booking['bus_id'] == 2) $busClass = 'bus-2';
                      else if($booking['bus_id'] == 3) $busClass = 'bus-3';
                    ?>
                    <span class="status <?= $busClass ?>">Bus #<?= htmlspecialchars($booking['bus_id']) ?></span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center">No bookings found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>