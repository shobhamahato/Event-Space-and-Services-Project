<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

/* FETCH BOOKINGS WITH USER + VENDOR DETAILS */

$query = "
SELECT 
    b.id,
    b.service_name,
    b.event_date,
    b.amount,
    b.booking_status,
    b.payment_status,

    u.name AS customer_name,
    v.business_name
FROM bookings b

LEFT JOIN users u
ON b.user_id = u.id

LEFT JOIN vendors v
ON b.vendor_id = v.vendor_id

";

$result = $conn->query($query);

/* TOTAL BOOKINGS */

$totalBookings = $conn->query("
SELECT COUNT(*) as total 
FROM bookings
")->fetch_assoc()['total'];

/* CONFIRMED BOOKINGS */

$confirmedBookings = $conn->query("
SELECT COUNT(*) as total 
FROM bookings 
WHERE booking_status='Confirmed'
")->fetch_assoc()['total'];

/* COMPLETED BOOKINGS */

$completedBookings = $conn->query("
SELECT COUNT(*) as total 
FROM bookings 
WHERE booking_status='Confirmed'
")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Bookings</title>

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
}

/* PAGE HEADER */

.page-header{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    border-radius:22px;
    padding:35px;
    color:white;

    box-shadow:
    0 10px 25px rgba(0,0,0,0.10);
}

/* INFO CARDS */

.info-card{
    background:white;
    border-radius:20px;
    padding:25px;
    text-align:center;

    box-shadow:
    0 8px 20px rgba(196,181,253,0.10),
    0 4px 10px rgba(221,214,254,0.08);

    transition:0.3s;
}

.info-card:hover{
    transform:translateY(-4px);
}

.info-card h6{
    color:#6b7280;
    margin-bottom:10px;
}

.info-card h3{
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

<a href="bookings.php" class="active">Bookings</a>

<a href="reports.php">Reports</a>

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
Manage Bookings
</h5>

</div>

<div class="container mt-4">

<!-- HEADER -->

<div class="page-header mb-4">

<h2>
Booking Management 📅
</h2>

<p class="mt-3 mb-0">
Track and monitor all platform booking activities professionally.
</p>

</div>

<!-- SUMMARY CARDS -->

<div class="row g-4 mb-4">

<div class="col-md-4">

<div class="info-card">

<h6>Total Bookings</h6>

<h3>
<?php echo $totalBookings; ?>
</h3>

</div>

</div>

<div class="col-md-4">

<div class="info-card">

<h6>Confirmed Bookings</h6>

<h3>
<?php echo $confirmedBookings; ?>
</h3>

</div>

</div>

<div class="col-md-4">

<div class="info-card">

<h6>Completed Bookings</h6>

<h3>
<?php echo $completedBookings; ?>
</h3>

</div>

</div>

</div>

<!-- TABLE SECTION -->

<div class="table-card">

<div class="table-responsive">

<table class="table align-middle">

<thead>

<tr>

<th>Booking ID</th>

<th>Customer</th>

<th>Vendor</th>

<th>Service</th>

<th>Event Date</th>

<th>Amount</th>

<th>Payment</th>

<th>Status</th>

<th>Booked On</th>

</tr>

</thead>

<tbody>

<?php if($result->num_rows > 0): ?>

<?php while($row = $result->fetch_assoc()): ?>

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
<?php echo htmlspecialchars($row['service_name']); ?>
</td>

<td>
<?php echo date("d M Y", strtotime($row['event_date'])); ?>
</td>

<td>
₹<?php echo number_format($row['amount'],2); ?>
</td>

<td>

<?php if($row['payment_status'] == 'Paid'): ?>

<span class="badge bg-success">
Paid
</span>

<?php else: ?>

<span class="badge bg-warning text-dark">
Pending
</span>

<?php endif; ?>

</td>

<td>

<?php if($row['booking_status'] == 'Pending'): ?>

<span class="badge bg-warning text-dark">
Pending
</span>

<?php elseif($row['booking_status'] == 'Confirmed'): ?>

<span class="badge bg-primary">
Confirmed
</span>

<?php elseif($row['booking_status'] == 'Completed'): ?>

<span class="badge bg-success">
Completed
</span>

<?php else: ?>

<span class="badge bg-danger">
Cancelled
</span>

<?php endif; ?>

</td>

<td>
<?php echo date("d M Y", strtotime($row['event_date'])); ?>
</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="9" class="text-center py-4">
No bookings found.
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