<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

/* TOTAL COUNTS */

$totalVendors = $conn->query("
SELECT COUNT(*) as total 
FROM vendors
")->fetch_assoc()['total'];

$totalUsers = $conn->query("
SELECT COUNT(*) as total 
FROM users
")->fetch_assoc()['total'];

$totalBookings = $conn->query("
SELECT COUNT(*) as total 
FROM bookings
")->fetch_assoc()['total'];

$totalRevenue = 0;

$result = $conn->query("
    SELECT SUM(admin_revenue) AS total_revenue 
    FROM bookings
");

if($result && $row = $result->fetch_assoc()){
    $totalRevenue = $row['total_revenue'] ?? 0;
}

/* BOOKING STATUS */

$pendingBookings = $conn->query("
SELECT COUNT(*) as total 
FROM bookings 
WHERE payment_status='pending'
")->fetch_assoc()['total'];

$confirmedBookings = $conn->query("
SELECT COUNT(*) as total 
FROM bookings 
WHERE booking_status='confirmed'
")->fetch_assoc()['total'];



/* RECENT BOOKINGS */

$recentBookings = $conn->query("
SELECT 
    b.id,
    b.customer_name,
    b.amount,
    b.payment_status,
    b.booking_status,
    v.business_name
FROM bookings b
LEFT JOIN vendors v 
ON b.vendor_id = v.vendor_id
ORDER BY b.id DESC
LIMIT 5
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Reports</title>

<link href="../assets/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background: linear-gradient(135deg,#eef2ff,#f8fbff);
    font-family:'Segoe UI',sans-serif;
}

/* SIDEBAR */

.sidebar{
    min-height:100vh;
    background: linear-gradient(180deg,#182848,#4b6cb7);
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
    background: linear-gradient(to right, #eef2ff, #f8f9ff);
    padding: 18px 25px;
    border-bottom: 1px solid #e6ebf5;

    box-shadow:
        0 4px 12px rgba(0,0,0,0.05),
        0 2px 4px rgba(0,0,0,0.03);

    position: relative;
    z-index: 100;
}

.topbar h5{
    color: #2c3e50;
    font-weight: 600;
    letter-spacing: 0.3px;
}

/* HEADER */

.page-header{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    border-radius:22px;
    padding:35px;
    color:white;

    box-shadow:
    0 10px 25px rgba(0,0,0,0.10);
}

/* REPORT CARDS */

.report-card{
    background:white;
    border-radius:20px;
    padding:25px;
    text-align:center;

    box-shadow:
    0 8px 20px rgba(196,181,253,0.10),
    0 4px 10px rgba(221,214,254,0.08);

    transition:0.3s;
}

.report-card:hover{
    transform:translateY(-4px);
}

.report-card h6{
    color:#6b7280;
    margin-bottom:10px;
}

.report-card h3{
    color:#182848;
    font-weight:bold;
}

/* TABLE CARD */

.table-card{
    background:white;
    border-radius:22px;
    padding:25px;

    box-shadow:
    0 10px 25px rgba(196,181,253,0.12),
    0 4px 10px rgba(221,214,254,0.08);
}

/* TABLE */

.table{
    border-radius:15px;
    overflow:hidden;
}

.table thead{
    background:linear-gradient(135deg,#4b6cb7,#182848);
    color:white;
}

.table thead th{
    border:none;
    padding:16px;
    font-weight:600;
}

.table td{
    padding:16px;
    vertical-align:middle;
}

.table tbody tr{
    transition:0.3s;
}

.table tbody tr:hover{
    background:#f8f7ff;
}

/* BADGES */

.badge{
    padding:8px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

</style>

</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->

<div class="col-md-2 sidebar p-0">

<h4 class="text-center py-4 border-bottom">
Admin Panel
</h4>

<a href="dashboard.php">Dashboard</a>

<a href="vendors.php">Vendors</a>

<a href="users.php">Users</a>

<a href="bookings.php">Bookings</a>

<a href="reports.php" class="active">Reports</a>

<a href="settings.php">Settings</a>

<a href="../auth/logout.php" class="text-warning">
Logout
</a>

</div>

<!-- MAIN CONTENT -->

<div class="col-md-10 p-0">

<!-- TOPBAR -->

<div class="topbar">

<h5 class="mb-0">
Reports & Analytics
</h5>

</div>

<div class="container mt-4">

<!-- HEADER -->

<div class="page-header mb-4">

<h2>
Platform Reports 📊
</h2>

<p class="mt-3 mb-0">
Monitor platform growth, booking analytics and revenue reports.
</p>

</div>

<!-- REPORT CARDS -->

<div class="row g-4 mb-4">

<div class="col-md-4">

<div class="report-card">

<h6>Total Vendors</h6>

<h3>
<?php echo $totalVendors; ?>
</h3>

</div>

</div>

<div class="col-md-4">

<div class="report-card">

<h6>Total Users</h6>

<h3>
<?php echo $totalUsers; ?>
</h3>

</div>

</div>

<div class="col-md-4">

<div class="report-card">


<h6>Total Revenue</h6>

<h3>
₹<?php echo number_format($totalRevenue,2); ?>
</h3>

</div>

</div>

</div>

<!-- STATUS REPORT -->

<div class="row g-4 mb-4">
    <div class="col-md-4">

<div class="report-card">

<h6>Total Bookings</h6>

<h3 class="text-primary">
<?php echo $totalBookings; ?>
</h3>

</div>

</div>


<div class="col-md-4">

<div class="report-card">

<h6>Pending Bookings</h6>

<h3 class="text-warning">
<?php echo $pendingBookings; ?>
</h3>

</div>

</div>

<div class="col-md-4">

<div class="report-card">

<h6>Confirmed Bookings</h6>

<h3 class="text-primary">
<?php echo $confirmedBookings; ?>
</h3>

</div>

</div>

</div>

<!-- RECENT BOOKINGS -->

<div class="table-card">

<h5 class="mb-4">
Recent Booking Activity
</h5>

<div class="table-responsive">

<table class="table align-middle">

<thead>

<tr>

<th>Booking ID</th>

<th>Customer</th>

<th>Vendor</th>

<th>Booking Amount</th>

<th>Payment Status</th>

<th>Booking Status</th>

</tr>

</thead>

<tbody>

<?php if($recentBookings->num_rows > 0): ?>

<?php while($row = $recentBookings->fetch_assoc()): ?>

<tr>

<td>
#<?php echo $row['id']; ?>
</td>

<td>
<?php echo htmlspecialchars($row['customer_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['business_name']); ?>
</td>

<td>
₹<?php echo number_format($row['amount'],2); ?>
</td>

<!-- PAYMENT STATUS -->

<td>

<?php if(strtolower($row['payment_status']) == 'pending'): ?>

<span class="badge bg-warning text-dark">
Pending
</span>

<?php elseif(strtolower($row['payment_status']) == 'completed'): ?>

<span class="badge bg-success">
Completed
</span>

<?php elseif(strtolower($row['payment_status']) == 'failed'): ?>

<span class="badge bg-danger">
Failed
</span>

<?php else: ?>

<span class="badge bg-secondary">
<?php echo ucfirst($row['payment_status']); ?>
</span>

<?php endif; ?>

</td>

<!-- BOOKING STATUS -->

<td>

<?php if(strtolower($row['booking_status']) == 'pending'): ?>

<span class="badge bg-warning text-dark">
Pending
</span>

<?php elseif(strtolower($row['booking_status']) == 'confirmed'): ?>

<span class="badge bg-primary">
Confirmed
</span>

<?php elseif(strtolower($row['booking_status']) == 'completed'): ?>

<span class="badge bg-success">
Completed
</span>

<?php elseif(strtolower($row['booking_status']) == 'cancelled'): ?>

<span class="badge bg-danger">
Cancelled
</span>

<?php else: ?>

<span class="badge bg-secondary">
<?php echo ucfirst($row['booking_status']); ?>
</span>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="6" class="text-center py-4">
No booking records found.
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
</div>

</body>
</html>