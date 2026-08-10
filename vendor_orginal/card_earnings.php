<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['vendor_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$current_month = date("Y-m");

/* ---------------- TOTAL EARNINGS ---------------- */
$stmt = $conn->prepare("
    SELECT SUM(total_amount) as total 
    FROM bookings 
    WHERE vendor_id=? AND status='completed'
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$total_earnings = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
if($total_earnings == null) $total_earnings = 0;

/* ---------------- MONTHLY EARNINGS ---------------- */
$stmt = $conn->prepare("
    SELECT SUM(total_amount) as monthly_total 
    FROM bookings 
    WHERE vendor_id=? 
    AND status='completed'
    AND DATE_FORMAT(event_date, '%Y-%m') = ?
");
$stmt->bind_param("is", $vendor_id, $current_month);
$stmt->execute();
$monthly_earnings = $stmt->get_result()->fetch_assoc()['monthly_total'] ?? 0;
if($monthly_earnings == null) $monthly_earnings = 0;

/* ---------------- COMPLETED BOOKINGS COUNT ---------------- */
$stmt = $conn->prepare("
    SELECT COUNT(*) as completed_count 
    FROM bookings 
    WHERE vendor_id=? AND status='completed'
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$completed_count = $stmt->get_result()->fetch_assoc()['completed_count'] ?? 0;

/* ---------------- EARNINGS HISTORY ---------------- */
$stmt = $conn->prepare("
    SELECT b.*, s.service_name
    FROM bookings b
    JOIN services s ON b.service_id = s.service_id
    WHERE b.vendor_id=? AND b.status='completed'
    ORDER BY b.event_date DESC
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$earnings_history = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Earnings</title>
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
            <h5 class="mb-0">Earnings Overview</h5>
        </div>

        <div class="container mt-4">

            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card card-box shadow-sm p-3 text-center">
                        <h6>Total Earnings</h6>
                        <h3>₹<?php echo number_format($total_earnings,2); ?></h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-box shadow-sm p-3 text-center">
                        <h6>This Month</h6>
                        <h3>₹<?php echo number_format($monthly_earnings,2); ?></h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-box shadow-sm p-3 text-center">
                        <h6>Completed Bookings</h6>
                        <h3><?php echo $completed_count; ?></h3>
                    </div>
                </div>
            </div>

            <!-- Earnings History Table -->
            <div class="card shadow-sm p-4">
                <h5>Completed Bookings History</h5>
                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Event Date</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $earnings_history->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td><?php echo $row['event_date']; ?></td>
                            <td>₹<?php echo number_format($row['total_amount'],2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($earnings_history->num_rows == 0): ?>
                        <tr>
                            <td colspan="4" class="text-center">No completed bookings yet.</td>
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