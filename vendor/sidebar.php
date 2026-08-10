<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../config/db.php");

if(!isset($_SESSION['vendor_id'])){
    header("Location: login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* FETCH VENDOR DATA */
$query = "SELECT vendor_type FROM vendors WHERE vendor_id='$vendor_id'";
$result = $conn->query($query);
$vendor = $result->fetch_assoc();

$vendor_type = $vendor['vendor_type'];

/* SERVICE PAGE PATH */
if($vendor_type == "card_vendor"){
    $service_page = "card_templates.php";   // changed here
}else{
    $service_page = "services.php";
}
?>

<!-- SIDEBAR -->
<div class="col-md-2 sidebar p-0" style="background:#0b1b35; min-height:100vh;">

<h4 class="text-center text-white py-4 border-bottom">
EventSpace
</h4>

<a href="dashboard.php" class="d-block text-white px-4 py-2">Dashboard</a>

<a href="profile.php" class="d-block text-white px-4 py-2">Profile</a>

<a href="<?php echo $service_page; ?>" class="d-block text-white px-4 py-2">
Services
</a>

<?php
/* SHOW PACKAGES ONLY FOR NON CARD VENDORS */
if($vendor_type != "card-vendor"){
?>
<a href="packages.php" class="d-block text-white px-4 py-2">
Packages
</a>
<?php } ?>

<?php
/* CARD VENDOR EXTRA MENU */
if($vendor_type == "card_vendor"){
?>
<a href="card_services.php" class="d-block text-white px-4 py-2">
Upload Template
</a>

<a href="card_templates.php" class="d-block text-white px-4 py-2">
My Templates
</a>
<?php } ?>

<a href="portfolio.php" class="d-block text-white px-4 py-2">
Portfolio
</a>

<!-- <a href="availability.php" class="d-block text-white px-4 py-2">
Availability
</a> -->

<a href="bookings.php" class="d-block text-white px-4 py-2">
Bookings
</a>

<a href="earnings.php" class="d-block text-white px-4 py-2">
Earnings
</a>

<a href="reviews.php" class="d-block text-white px-4 py-2">
Reviews
</a>

<a href="../auth/logout.php" class="d-block text-white px-4 py-2">
Logout
</a>

</div>

<style>
.sidebar a:hover{
background:#1f3b6d;
text-decoration:none;
}
</style>