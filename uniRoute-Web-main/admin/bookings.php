<?php
session_start();
include 'connection.php';

// Handle filters
$bus_id_filter = isset($_GET['bus_id']) ? $_GET['bus_id'] : '';
$date_filter = isset($_GET['reserved_at']) ? $_GET['reserved_at'] : '';

// Fetch distinct bus IDs for filter dropdown
$busIds = $pdo->query("SELECT DISTINCT bus_id FROM customers")->fetchAll(PDO::FETCH_COLUMN);

// Prepare query with optional filters
$query = "SELECT * FROM customers WHERE 1";
$params = [];

if ($bus_id_filter !== '') {
    $query .= " AND bus_id = ?";
    $params[] = $bus_id_filter;
}

if ($date_filter !== '') {
    $query .= " AND DATE(reserved_at) = ?";
    $params[] = $date_filter;
}

// Add ordering to group by bus_id
$query .= " ORDER BY bus_id";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group bookings by bus_id
$bookingsByBus = [];
foreach ($bookings as $booking) {
    $busId = $booking['bus_id'];
    if (!isset($bookingsByBus[$busId])) {
        $bookingsByBus[$busId] = [];
    }
    $bookingsByBus[$busId][] = $booking;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .bus-card {
            margin-bottom: 2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .bus-header {
            background-color: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .customer-count {
            background-color: #0d6efd;
            color: white;
            border-radius: 50%;
            padding: 0.2rem 0.6rem;
            font-size: 0.8rem;
            margin-left: 10px;
        }
        .booking-table th, .booking-table td {
            padding: 0.75rem;
        }
        .filter-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .page-title {
            color: #0d6efd;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include '../admin/adminLayout/header.php'; ?>

<div class="container" style="margin-top: 85px">
    <h2 class="page-title">Seats Reserved</h2>

    <div class="filter-card">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label for="bus_id" class="form-label fw-bold">Filter by Bus ID</label>
                <select id="bus_id" name="bus_id" class="form-select">
                    <option value="">All Buses</option>
                    <?php foreach ($busIds as $id): ?>
                        <option value="<?= htmlspecialchars($id) ?>" <?= $bus_id_filter == $id ? 'selected' : '' ?>>
                            Bus <?= htmlspecialchars($id) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-5">
                <label for="reserved_at" class="form-label fw-bold">Reservation Date</label>
                <input type="date" id="reserved_at" name="reserved_at" class="form-control"
                       value="<?= htmlspecialchars($date_filter) ?>">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>

    <?php if (count($bookingsByBus) > 0): ?>
        <div class="row">
            <?php foreach ($bookingsByBus as $busId => $busPeople): ?>
                <div class="col-12">
                    <div class="bus-card">
                        <div class="bus-header d-flex justify-content-between align-items-center">
                            <h4>
                                Bus <?= htmlspecialchars($busId) ?>
                                <span class="customer-count"><?= count($busPeople) ?></span>
                            </h4>
                            <span class="text-muted">Total passengers: <?= count($busPeople) ?></span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover booking-table mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Reserved At</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($busPeople as $person): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($person['id']) ?></td>
                                        <td><?= htmlspecialchars($person['name']) ?></td>
                                        <td><?= htmlspecialchars($person['phone']) ?></td>
                                        <td><?= htmlspecialchars($person['reserved_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            No bookings found for the selected criteria.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>