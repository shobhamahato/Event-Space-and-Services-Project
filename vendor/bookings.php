<?php 
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['vendor_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

$today = date("Y-m-d");

/* ---------------- HANDLE ACTIONS ---------------- */

if(isset($_GET['action']) && isset($_GET['booking_id'])){

    $booking_id = intval($_GET['booking_id']);
    $action = $_GET['action'];

    /* ACCEPT BOOKING */
    if($action == "accept"){

        $status = "Confirmed";

        $update = $conn->prepare("
            UPDATE bookings 
            SET booking_status=? 
            WHERE id=? AND vendor_id=?
        ");

        $update->bind_param("sii", $status, $booking_id, $vendor_id);
        $update->execute();
        /*
====================================================
FETCH BOOKING DETAILS
====================================================
*/

$bookingQuery = $conn->prepare("
    SELECT * FROM bookings
    WHERE id = ?
");

$bookingQuery->bind_param("i", $booking_id);
$bookingQuery->execute();

$bookingResult = $bookingQuery->get_result();

$booking = $bookingResult->fetch_assoc();

$user_id = $booking['user_id'];

$service_name = $booking['service_name'];

/*
====================================================
NOTIFICATION 1
====================================================
*/

$title1 = "Booking Approved";

$message1 = "Vendor approved your booking request for ".$service_name.".";

$type1 = "approved";

$insert1 = $conn->prepare("
    INSERT INTO notifications
    (user_id, booking_id, title, message, type)
    VALUES (?, ?, ?, ?, ?)
");

$insert1->bind_param(
    "iisss",
    $user_id,
    $booking_id,
    $title1,
    $message1,
    $type1
);

$insert1->execute();

/*
====================================================
NOTIFICATION 2
====================================================
*/

$title2 = "Payment Required";

$message2 = "Pay the amount to confirm your booking for ".$service_name.".";

$type2 = "payment";

$insert2 = $conn->prepare("
    INSERT INTO notifications
    (user_id, booking_id, title, message, type)
    VALUES (?, ?, ?, ?, ?)
");

$insert2->bind_param(
    "iisss",
    $user_id,
    $booking_id,
    $title2,
    $message2,
    $type2
);

$insert2->execute();
    }

    /* REJECT BOOKING */
    elseif($action == "reject"){

        $status = "Cancelled";

        $update = $conn->prepare("
            UPDATE bookings 
            SET booking_status=? 
            WHERE id=? AND vendor_id=?
        ");

        $update->bind_param("sii", $status, $booking_id, $vendor_id);
        $update->execute();
    }

    /* DELETE BOOKING */
    elseif($action == "delete"){

        $delete = $conn->prepare("
            DELETE FROM bookings 
            WHERE id=? AND vendor_id=?
        ");

        $delete->bind_param("ii", $booking_id, $vendor_id);
        $delete->execute();
    }

    header("Location: bookings.php");
    exit();
}

/* ---------------- FETCH UPCOMING BOOKINGS ---------------- */

$stmt = $conn->prepare("
    SELECT *
    FROM bookings
    WHERE vendor_id = ? AND event_date >= ?
    ORDER BY event_date ASC
");

$stmt->bind_param("is", $vendor_id, $today);

$stmt->execute();

$upcoming = $stmt->get_result();

/* ---------------- FETCH BOOKING HISTORY ---------------- */

$stmt2 = $conn->prepare("
    SELECT *
    FROM bookings
    WHERE vendor_id = ? AND event_date < ?
    ORDER BY event_date DESC
");

$stmt2->bind_param("is", $vendor_id, $today);

$stmt2->execute();

$history = $stmt2->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>Vendor Bookings</title>

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
    background:white;
    padding:18px 25px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

/* HEADER */

.booking-header{
    background: linear-gradient(135deg,#5b86e5,#36d1dc);
    border-radius:22px;
    padding:35px;
    color:white;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

/* TABLE CARD */

.table-card{
    background:white;
    border-radius:22px;
    padding:30px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
    margin-bottom:30px;
}

/* TABLE */

.table{
    overflow:hidden;
    border-radius:15px;
}

.table thead{
    background:linear-gradient(135deg,#5b86e5,#36d1dc);
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
    background:#f9f7ff;
}

/* BADGES */

.status-badge{
    padding:8px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.completed{
    background:#e8fff1;
    color:#198754;
}

.confirmed{
    background:#eaf2ff;
    color:#0d6efd;
}

.pending{
    background:#fff6e5;
    color:#ff9800;
}

.cancelled{
    background:#ffeaea;
    color:#dc3545;
}

/* EMPTY TEXT */

.empty-text{
    padding:20px;
    text-align:center;
    color:#6c757d;
}

/* SECTION TITLE */

.section-title{
    font-weight:600;
    margin-bottom:20px;
    color:#374151;
}

/* ACTION BUTTONS */

.action-btn{
    padding:7px 12px;
    border:none;
    border-radius:8px;
    color:white;
    text-decoration:none;
    font-size:13px;
    margin-right:5px;
    transition:0.3s;
    display:inline-block;
}

.accept-btn{
    background:#198754;
}

.reject-btn{
    background:#dc3545;
}

.delete-btn{
    background:#6c757d;
}

.action-btn:hover{
    opacity:0.9;
    color:white;
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
<a href="availability.php">Availability</a>
<a href="bookings.php" class="active">Bookings</a>
<a href="earnings.php">Earnings</a>
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
Manage Bookings
</h5>

</div>

<div class="container mt-4">

<!-- HEADER -->

<div class="booking-header mb-4">

<h2>
Booking Management 📖
</h2>

<p class="mt-3 mb-0">
Track your upcoming events and manage your booking history professionally.
</p>

</div>

<!-- UPCOMING BOOKINGS -->

<!-- UPCOMING BOOKINGS -->

<div class="table-card">

<h5 class="section-title">
Upcoming Bookings
</h5>

<table class="table">

<thead>

<tr>

<th>Customer</th>
<th>Service</th>
<th>Event Date</th>
<th>Event Address</th>
<th>Amount</th>
<th>Status</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php if($upcoming->num_rows > 0): ?>

<?php while($row = $upcoming->fetch_assoc()): ?>

<tr>

<td>

<?php echo htmlspecialchars($row['customer_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['service_name']); ?>

</td>

<td>

<?php echo date("d M Y", strtotime($row['event_date'])); ?>

</td>

<td>

<?php echo htmlspecialchars($row['event_location']); ?>

</td>

<td>

₹<?php echo number_format($row['amount'],2); ?>

</td>

<td>

<?php
$status = strtolower($row['booking_status']);
?>

<span class="status-badge <?php echo $status; ?>">

<?php echo ucfirst($row['booking_status']); ?>

</span>

</td>

<td>

<?php if($status == "confirmed"): ?>

<!-- ONLY DELETE BUTTON -->

<a href="bookings.php?action=delete&booking_id=<?php echo $row['id']; ?>" 
class="action-btn delete-btn"
onclick="return confirm('Are you sure you want to delete this booking?')">

Delete

</a>

<?php elseif($status == "pending"): ?>

<!-- ACCEPT BUTTON -->

<a href="bookings.php?action=accept&booking_id=<?php echo $row['id']; ?>" 
class="action-btn accept-btn">

Accept

</a>

<!-- REJECT BUTTON -->

<a href="bookings.php?action=reject&booking_id=<?php echo $row['id']; ?>" 
class="action-btn reject-btn">

Reject

</a>

<!-- DELETE BUTTON -->

<a href="bookings.php?action=delete&booking_id=<?php echo $row['id']; ?>" 
class="action-btn delete-btn"
onclick="return confirm('Are you sure you want to delete this booking?')">

Delete

</a>

<?php else: ?>

<!-- ONLY DELETE FOR OTHER STATUSES -->

<a href="bookings.php?action=delete&booking_id=<?php echo $row['id']; ?>" 
class="action-btn delete-btn"
onclick="return confirm('Are you sure you want to delete this booking?')">

Delete

</a>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="7" class="empty-text">
No upcoming bookings available.
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

<!-- BOOKING HISTORY -->

<div class="table-card">

<h5 class="section-title">
Booking History
</h5>

<table class="table">

<thead>

<tr>

<th>Customer</th>
<th>Service</th>
<th>Event Date</th>
<th>Amount</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php if($history->num_rows > 0): ?>

<?php while($row = $history->fetch_assoc()): ?>

<tr>

<td>

<?php echo htmlspecialchars($row['customer_name']); ?>

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

<?php
$status = strtolower($row['booking_status']);
?>

<span class="status-badge <?php echo $status; ?>">

<?php echo ucfirst($row['booking_status']); ?>

</span>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="5" class="empty-text">
No booking history available.
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