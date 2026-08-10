<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* UPDATE PACKAGES */
if(isset($_POST['update_packages'])){

$packages = trim($_POST['packages']);

$stmt = $conn->prepare("UPDATE photography_vendor SET packages=? WHERE vendor_id=?");
$stmt->bind_param("si",$packages,$vendor_id);
$stmt->execute();

header("Location: packages.php?success=updated");
exit();

}

/* FETCH PACKAGES FROM photography_vendor */

$stmt = $conn->prepare("SELECT packages FROM photography_vendors WHERE vendor_id=?");
$stmt->bind_param("i",$vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$current_packages = $row['packages'] ?? "";

?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Packages</title>

<link href="../assets/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
font-family:'Segoe UI';
}

.sidebar{
height:100vh;
background:#1e1e2f;
position:fixed;
}

.sidebar a{
color:#c2c7d0;
padding:14px 20px;
display:block;
text-decoration:none;
}

.sidebar a:hover,
.sidebar .active{
background:linear-gradient(90deg,#4e73df,#1cc88a);
color:#fff;
}

.topbar{
background:#fff;
padding:18px 25px;
box-shadow:0 3px 10px rgba(0,0,0,0.05);
}

.main-content{
margin-left:16.6%;
}

.card-box{
background:#fff;
padding:25px;
border-radius:15px;
box-shadow:0 6px 18px rgba(0,0,0,0.06);
}

.btn-gradient{
background:linear-gradient(135deg,#4e73df,#1cc88a);
border:none;
color:#fff;
padding:8px 20px;
border-radius:25px;
}

</style>

</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- Sidebar -->

<div class="col-md-2 sidebar p-0">

<h4 class="text-center py-4 border-bottom text-white">
EventSpace
</h4>
 

<a href="dashboard.php">Dashboard</a>
<a href="profile.php">Profile</a>
<a href="services.php">Services</a>
<a href="packages.php" class="active">Packages</a>
<a href="portfolio.php">Portfolio</a>
<a href="availability.php">Availability</a>
<a href="bookings.php">Bookings</a>
<a href="earnings.php">Earnings</a>
<a href="reviews.php">Reviews</a>
<a href="../auth/logout.php" class="text-danger">Logout</a>

</div>

<!-- Main Content -->

<div class="col-md-10 offset-md-2 main-content p-0">

<div class="topbar">
<h5>Manage Packages</h5>
</div>

<div class="container mt-4">

<?php if(isset($_GET['success'])): ?>

<div class="alert alert-success">
Packages updated successfully!
</div>

<?php endif; ?>

<!-- Update Packages -->

<div class="card-box">

<h5>Photography Packages</h5>

<form method="POST">

<label class="mb-2">Edit your packages</label>

<textarea 
name="packages" 
class="form-control" 
rows="8"
placeholder="Example:

Basic Package – ₹10000
Wedding Package – ₹25000
Premium Package – ₹40000
"><?php echo htmlspecialchars($current_packages); ?></textarea>

<div class="text-end mt-3">

<button type="submit" 
name="update_packages" 
class="btn-gradient">

Update Packages

</button>

</div>

</form>

</div>

<!-- Display Packages -->

<div class="card-box mt-4">

<h5>Your Current Packages</h5>

<?php if(!empty($current_packages)): ?>

<pre><?php echo htmlspecialchars($current_packages); ?></pre>

<?php else: ?>

<div class="alert alert-info">
No packages available.
</div>

<?php endif; ?>

</div>

</div>
</div>
</div>
</div>

</body>
</html>