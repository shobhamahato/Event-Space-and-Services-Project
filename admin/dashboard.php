
<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$totalVendors = $conn->query("SELECT COUNT(*) as total FROM vendors")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$pendingVendors = $conn->query("SELECT COUNT(*) as total FROM vendors WHERE status='pending'")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

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
/* ADMIN BADGE */

.admin-badge{
     background: linear-gradient(to right, #355c9a, #4b6cb7);
    color:white;
    border: 1px solid #c9d8ff;
    border-radius: 20px;
    display: inline-block;
    transition: 0.3s ease;
}



/* TOPBAR */

.topbar{
    background: linear-gradient(to right, #eef2ff, #f8f9ff);
    padding: 18px 25px;
    border-bottom: 1px solid #e6ebf5;

    /* SOFT PROFESSIONAL SHADOW */
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

/* DASHBOARD HEADER */

.dashboard-header{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    border-radius:22px;
    padding:35px;
    color:white;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

/* CARDS */

.card-box{
    background: linear-gradient(135deg,#faf5ff,#f5f3ff);
    border-radius:22px;
    padding:30px;
    border:1px solid #ede9fe;

    transition:0.3s;

    box-shadow:
    0 10px 25px rgba(196,181,253,0.18),
    0 4px 10px rgba(221,214,254,0.12);
}

.card-box:hover{
    transform:translateY(-5px);
    box-shadow:
    0 14px 28px rgba(196,181,253,0.22),
    0 6px 14px rgba(221,214,254,0.16);
}

/* ICON CIRCLE */

.icon-circle{
    width:65px;
    height:65px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    margin-bottom:18px;
    font-size:24px;
    font-weight:bold;
    color:white;
}

.bg-blue{
    background:linear-gradient(135deg,#5b86e5,#36d1dc);
}

.bg-orange{
    background:linear-gradient(135deg,#f7971e,#ffd200);
}

.bg-green{
    background:linear-gradient(135deg,#11998e,#38ef7d);
}

/* NUMBERS */

.card-box h2{
    font-size:38px;
    font-weight:700;
    margin-top:10px;
    color:#374151;
}

.card-box h6{
    color:#6b7280;
    font-weight:600;
    letter-spacing:0.5px;
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

<a href="dashboard.php" class="active">Dashboard</a>
<a href="vendors.php">Vendors</a>
<a href="users.php">Users</a>
<a href="bookings.php">Bookings</a>
<a href="reports.php">Reports</a>
<a href="settings.php">Settings</a>

<a href="../auth/logout.php" class="text-warning">
Logout
</a>

</div>

<!-- MAIN CONTENT -->

<div class="col-md-10 p-0">

<!-- TOPBAR -->

<div class="topbar d-flex justify-content-between align-items-center">

<h5 class="mb-0">
Dashboard Overview
</h5>

<span class="admin-badge fw-semibold px-3 py-2">
    Welcome Admin
</span>

</div>

<div class="container mt-4">

<!-- HEADER -->

<div class="dashboard-header mb-4">

<h2>
Admin Dashboard ✨
</h2>

<p class="mt-3 mb-0">
Manage vendors, users, bookings and platform activities professionally.
</p>

</div>

<!-- STATS CARDS -->

<div class="row g-4">

<!-- TOTAL VENDORS -->

<div class="col-md-4">

<div class="card-box text-center">

<div class="icon-circle bg-blue">
👨‍💼
</div>

<h6>Total Vendors</h6>

<h2>
<?php echo $totalVendors; ?>
</h2>

</div>

</div>

<!-- PENDING APPROVALS -->

<div class="col-md-4">

<div class="card-box text-center">

<div class="icon-circle bg-orange">
⏳
</div>

<h6>Pending Approvals</h6>

<h2>
<?php echo $pendingVendors; ?>
</h2>

</div>

</div>

<!-- TOTAL USERS -->

<div class="col-md-4">

<div class="card-box text-center">

<div class="icon-circle bg-green">
👥
</div>

<h6>Total Users</h6>

<h2>
<?php echo $totalUsers; ?>
</h2>

</div>

</div>

</div>
<!-- total revenue  -->
 <?php $totalRevenue = 0;

$result = $conn->query("
    SELECT SUM(admin_revenue) AS total_revenue 
    FROM bookings
");

if($result && $row = $result->fetch_assoc()){
    $totalRevenue = $row['total_revenue'] ?? 0;
}?>
<div class="col-md-4">

<div class="card-box text-center">

<div class="icon-circle bg-green">
💰
</div>

<h6>Total Revenue</h6>

<h2>
₹<?php echo $totalRevenue; ?>
</h2>

</div>

</div>

</div>
</div>

</div>

</div>
</div>

</body>
</html>
