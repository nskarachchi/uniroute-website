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

// Fetch routes
$sql = "SELECT route_id, route, depature FROM route ORDER BY route_id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Routes - UniRoute</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;

    }
    .table-container {
      margin: 75px auto;
      max-width: 1000px;
    }
  </style>
</head>
<body>
<?php include '../common/header.php'; ?>


<div class="table-container container bg-white p-4 shadow rounded">
  <h4 class="mb-4" style="text-align: center;">Shuttle Time Table</h4>
  <?php if (mysqli_num_rows($result) > 0): ?>
    <table class="table table-striped table-bordered">
      <thead class="table-success">
        <tr>
          <th>Route</th>
          <th>Departure</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['route']); ?></td>
            <td><?php echo htmlspecialchars($row['depature']); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="text-danger">No routes found.</p>
  <?php endif; ?>
</div>

<?php include '../common/footer.html'; ?>

</body>
</html>
