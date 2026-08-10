<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['vendor_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$today = date("Y-m-d");

/* ---------------- FETCH UPCOMING BOOKINGS ---------------- */
$stmt = $conn->prepare("
    SELECT b.*, s.service_name 
    FROM bookings b
    JOIN services s ON b.service_id = s.service_id
    WHERE b.vendor_id = ? AND b.event_date >= ?
    ORDER BY b.event_date ASC
");
$stmt->bind_param("is", $vendor_id, $today);
$stmt->execute();
$upcoming = $stmt->get_result();

/* ---------------- FETCH BOOKING HISTORY ---------------- */
$stmt = $conn->prepare("
    SELECT b.*, s.service_name 
    FROM bookings b
    JOIN services s ON b.service_id = s.service_id
    WHERE b.vendor_id = ? AND b.event_date < ?
    ORDER BY b.event_date DESC
");
$stmt->bind_param("is", $vendor_id, $today);
$stmt->execute();
$history = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Bookings</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <style>
       body { background-color: #f8f9fa; }
        .sidebar { height: 100vh; background: #212529; color: #fff; }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }
        .sidebar a:hover { background: #343a40; color: #fff; }
        .sidebar .active { background: linear-gradient(135deg,#4e73df,#1cc88a);; color: #fff; }
        .topbar {
            background: #ffffff;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .card-box { border-radius: 15px; }
    </style>
</head>

<body>
<div class="container-fluid">
<div class="row">

    <!-- Sidebar -->
  <div class="col-md-2 sidebar p-0">
        <h4 class="text-center py-4 border-bottom">EventSpace</h4>

        <a href="card_dashboard.php" class="active">Dashboard</a>
        <a href="card_profile.php">Profile</a>
        <a href="card_services.php">Upload templates</a>
        <a href="card_templates.php">My Templates</a>
        <a href="card_portfolio.php">Portfolio</a>
        <a href="card_availability.php">Availability</a>
        <a href="card_bookings.php">Bookings</a>
        <a href="card_earnings.php">Earnings</a>
        <a href="card_reviews.php">Reviews</a>
        <a href="../auth/logout.php" class="text-danger">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-10 p-0">

        <div class="topbar">
            <h5 class="mb-0">Manage Bookings</h5>
        </div>

        <div class="container mt-4">

            <!-- UPCOMING BOOKINGS -->
            <div class="card shadow-sm p-4 mb-4">
                <h5>Upcoming Bookings</h5>
                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Event Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $upcoming->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo $row['event_date']; ?></td>
                            <td>₹<?php echo number_format($row['total_amount'],2); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $row['status']=='completed' ? 'success' :
                                        ($row['status']=='confirmed' ? 'primary' :
                                        ($row['status']=='cancelled' ? 'danger' : 'warning'));
                                ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($upcoming->num_rows == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center">No upcoming bookings.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- BOOKING HISTORY -->
            <div class="card shadow-sm p-4">
                <h5>Booking History</h5>
                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Event Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $history->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo $row['event_date']; ?></td>
                            <td>₹<?php echo number_format($row['total_amount'],2); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $row['status']=='completed' ? 'success' :
                                        ($row['status']=='confirmed' ? 'primary' :
                                        ($row['status']=='cancelled' ? 'danger' : 'secondary'));
                                ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($history->num_rows == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center">No booking history.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
</div>
</body>
</html>