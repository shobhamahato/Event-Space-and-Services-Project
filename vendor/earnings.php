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
    SELECT SUM(amount) as total 
    FROM bookings 
    WHERE vendor_id=? 
    AND payment_status='paid'
");

$stmt->bind_param("i", $vendor_id);

$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

$total_earnings = $result['total'] ?? 0;

if($total_earnings == null){
    $total_earnings = 0;
}

/* ---------------- MONTHLY EARNINGS ---------------- */

$stmt = $conn->prepare("
    SELECT SUM(amount) as monthly_total 
    FROM bookings 
    WHERE vendor_id=? 
    AND payment_status='paid'
    AND DATE_FORMAT(event_date, '%Y-%m') = ?
");

$stmt->bind_param("is", $vendor_id, $current_month);

$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

$monthly_earnings = $result['monthly_total'] ?? 0;

if($monthly_earnings == null){
    $monthly_earnings = 0;
}

/* ---------------- COMPLETED BOOKINGS COUNT ---------------- */

$stmt = $conn->prepare("
    SELECT COUNT(*) as completed_count 
    FROM bookings 
    WHERE vendor_id=? 
    AND payment_status='paid'
");

$stmt->bind_param("i", $vendor_id);

$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();

$completed_count = $result['completed_count'] ?? 0;

/* ---------------- EARNINGS HISTORY ---------------- */

$stmt = $conn->prepare("
    SELECT b.*, u.name AS customer_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    WHERE b.vendor_id=? 
    AND b.payment_status='paid'
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

body{
    background:linear-gradient(135deg,#eef2ff,#f8fbff);
    font-family:'Segoe UI',sans-serif;
}

/* SIDEBAR */

.sidebar{
    min-height:100vh;
    background:linear-gradient(180deg,#182848,#4b6cb7);
    color:white;
}

.sidebar h4{
    font-weight:bold;
    letter-spacing:1px;
}

.sidebar a{
    color:#dfe6f1;
    text-decoration:none;
    display:block;
    padding:14px 22px;
    margin:6px 10px;
    border-radius:10px;
    transition:0.3s;
    font-size:15px;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.15);
    color:white;
    transform:translateX(4px);
}

.sidebar .active{
    background:white;
    color:#2c3e50;
    font-weight:600;
}

/* TOPBAR */

.topbar{
    background:white;
    padding:18px 25px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

/* HEADER CARD */

.earnings-banner{
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:white;
    border-radius:22px;
    padding:35px;
    box-shadow:0 8px 22px rgba(0,0,0,0.08);
}

.earnings-banner h2{
    font-weight:bold;
}

/* SUMMARY CARDS */

.summary-card{
    background:white;
    border-radius:20px;
    padding:25px;
    text-align:center;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
    transition:0.3s;
    border:none;
}

.summary-card:hover{
    transform:translateY(-5px);
}

.icon-box{
    width:60px;
    height:60px;
    margin:auto;
    margin-bottom:15px;
    border-radius:15px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:25px;
}

/* ICON COLORS */

.icon-purple{
    background:#efe8ff;
    color:#764ba2;
}

.icon-blue{
    background:#e8f0ff;
    color:#4b6cb7;
}

.icon-green{
    background:#e9fff2;
    color:#198754;
}

/* TABLE CARD */

.table-card{
    background:white;
    border-radius:22px;
    padding:30px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

/* TABLE */

.table{
    overflow:hidden;
    border-radius:15px;
}

.table thead{
    background:linear-gradient(135deg,#667eea,#764ba2);
    color:white;
}

.table thead th{
    border:none;
    padding:15px;
}

.table tbody td{
    padding:16px;
    vertical-align:middle;
}

.table tbody tr{
    transition:0.3s;
}

.table tbody tr:hover{
    background:#f7f4ff;
}

/* BADGE */

.amount-badge{
    background:#efe8ff;
    color:#764ba2;
    padding:8px 14px;
    border-radius:20px;
    font-weight:600;
}

.empty-text{
    text-align:center;
    padding:20px;
    color:#6c757d;
}

</style>

</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->

<div class="col-md-2 sidebar p-0">

<h4 class="text-center py-4 border-bottom">
EventSpace
</h4>

<a href="dashboard.php">Dashboard</a>
<a href="profile.php">Profile</a>
<a href="services.php">Services</a>
<a href="packages.php">Packages</a>
<a href="portfolio.php">Portfolio</a>
<!-- <a href="availability.php">Availability</a> -->
<a href="bookings.php">Bookings</a>
<a href="earnings.php" class="active">Earnings</a>
<a href="reviews.php">Reviews</a>

<a href="../auth/logout.php" class="text-warning">
Logout
</a>

</div>

<!-- MAIN CONTENT -->

<div class="col-md-10 p-0">

<!-- TOPBAR -->

<div class="topbar">

<h5 class="mb-0">
Earnings Overview
</h5>

</div>

<div class="container mt-4">

<!-- BANNER -->

<div class="earnings-banner mb-4">

<h2>
Your Earnings Dashboard 💰
</h2>

<p class="mt-3 mb-0">
Track your completed bookings, monthly revenue and overall business growth.
</p>

</div>

<!-- SUMMARY CARDS -->

<div class="row g-4 mb-4">

<!-- TOTAL EARNINGS -->

<div class="col-md-4">

<div class="summary-card">

<div class="icon-box icon-purple">
₹
</div>

<h6>Total Earnings</h6>

<h3 class="fw-bold">
₹<?php echo number_format($total_earnings,2); ?>
</h3>

<small class="text-muted">
Overall completed revenue
</small>

</div>

</div>

<!-- MONTHLY EARNINGS -->

<div class="col-md-4">

<div class="summary-card">

<div class="icon-box icon-blue">
📈
</div>

<h6>This Month</h6>

<h3 class="fw-bold">
₹<?php echo number_format($monthly_earnings,2); ?>
</h3>

<small class="text-muted">
Current month earnings
</small>

</div>

</div>

<!-- COMPLETED BOOKINGS -->

<div class="col-md-4">

<div class="summary-card">

<div class="icon-box icon-green">
✔
</div>

<h6>Completed Bookings</h6>

<h3 class="fw-bold">
<?php echo $completed_count; ?>
</h3>

<small class="text-muted">
Successfully completed events
</small>

</div>

</div>

</div>

<!-- EARNINGS HISTORY -->

<div class="table-card">

<h5 class="mb-4">
Completed Bookings History
</h5>

<table class="table">

<thead>

<tr>

<th>Customer</th>
<th>Booking Type</th>
<th>Event Date</th>
<th>Amount</th>

</tr>

</thead>

<tbody>

<?php if($earnings_history->num_rows > 0): ?>

<?php while($row = $earnings_history->fetch_assoc()): ?>

<tr>

<td>
<?php echo htmlspecialchars($row['customer_name']); ?>
</td>

<td>
Event Booking
</td>

<td>
<?php echo date("d M Y", strtotime($row['event_date'])); ?>
</td>

<td>

<span class="amount-badge">

₹<?php echo number_format($row['amount'],2); ?>

</span>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="4" class="empty-text">

No completed bookings yet.

</td>

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