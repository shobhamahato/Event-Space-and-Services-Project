
<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User List</title>

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

/* MAIN CARD */

.user-card{
    background: linear-gradient(135deg,#faf5ff,#f5f3ff);
    border-radius:22px;
    padding:30px;
    border:1px solid #ede9fe;

    box-shadow:
    0 10px 25px rgba(196,181,253,0.18),
    0 4px 10px rgba(221,214,254,0.12);
}

/* TABLE */

.table{
    border-radius:15px;
    overflow:hidden;
}

.table thead{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    color:white;
}

.table thead th{
    border:none;
    padding:16px;
    font-weight:600;
}

.table tbody tr{
    transition:0.3s;
}

.table tbody tr:hover{
    background:#f8f5ff;
    transform:scale(1.003);
}

.table tbody td{
    padding:16px;
    vertical-align:middle;
    border-color:#eee;
}

/* RESPONSIVE */

.table-responsive{
    border-radius:18px;
    overflow:hidden;
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

<a href="users.php" class="active">Users</a>

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
User List
</h5>

<span class="fw-semibold">
Admin Panel
</span>

</div>

<div class="container mt-4">

<!-- USER TABLE CARD -->

<div class="user-card">

<div class="d-flex justify-content-between align-items-center mb-4">

<h4 class="mb-0">
Registered Users
</h4>

<span class="badge bg-primary fs-6">
Total Users:
<?php echo $result->num_rows; ?>
</span>

</div>

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Mobile</th>
    <th>Address</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td>
<?php echo $row['id']; ?>
</td>

<td class="fw-semibold">
<?php echo htmlspecialchars($row['name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['email']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['mobile']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['address']); ?>
</td>

</tr>

<?php endwhile; ?>

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
