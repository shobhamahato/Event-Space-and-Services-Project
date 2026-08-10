<?php
session_start();
require_once("../config/db.php");

error_reporting(E_ALL);
ini_set('display_errors',1);

/* CHECK LOGIN */
if(!isset($_SESSION['vendor_id'])){
header("Location: ../auth/login.php");
exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* FETCH VENDOR DETAILS */
$stmt = $conn->prepare("SELECT business_name, vendor_type FROM vendors WHERE vendor_id=?");
$stmt->bind_param("i",$vendor_id);
$stmt->execute();
$vendor = $stmt->get_result()->fetch_assoc();

$business_name = $vendor['business_name'];
$vendor_type = $vendor['vendor_type'];


/* TEMPLATE UPLOAD */

if(isset($_POST['upload'])){

$template_name = $_POST['template_name'];
$event_id = $_POST['event_id'];
$price = $_POST['price'];


/* FILE CHECK */

if(isset($_FILES['image']) && $_FILES['image']['error']==0){

$image = $_FILES['image']['name'];
$tmp   = $_FILES['image']['tmp_name'];
$size  = $_FILES['image']['size'];

$allowed = ['jpg','jpeg','png','webp'];
$ext = strtolower(pathinfo($image,PATHINFO_EXTENSION));

if(!in_array($ext,$allowed)){
echo "<script>alert('Only JPG, PNG, WEBP allowed');</script>";
exit();
}

if($size > 5000000){
echo "<script>alert('Image must be under 5MB');</script>";
exit();
}


/* UPLOAD PATH (ABSOLUTE PATH FIX) */

$upload_dir = dirname(__DIR__) . "/uploads/templates/";

/* CREATE FOLDER IF NOT EXISTS */

if(!is_dir($upload_dir)){
mkdir($upload_dir,0777,true);
}

$new_name = time()."_".$image;

$path = $upload_dir.$new_name;


/* MOVE FILE */

if(move_uploaded_file($tmp,$path)){

$stmt = $conn->prepare("INSERT INTO templates
(vendor_id,event_id,template_name,image,price)
VALUES (?,?,?,?,?)");

$stmt->bind_param("iissd",
$vendor_id,
$event_id,
$template_name,
$new_name,
$price
);

if($stmt->execute()){

echo "<script>
alert('Template Uploaded Successfully');
window.location='card_templates.php';
</script>";

}else{

echo "<script>alert('Database Error');</script>";

}

}else{

echo "<script>alert('Image Upload Failed');</script>";

}

}else{

echo "<script>alert('Please select an image');</script>";

}

}

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Upload Invitation Template</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

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

<!-- SIDEBAR -->

<div class="col-md-2 sidebar p-0">

<h4 class="text-center py-4 border-bottom">EventSpace</h4>

<a href="card_dashboard.php">Dashboard</a>
<a href="card_profile.php">Profile</a>
<a href="card_services.php" class="active">Upload Templates</a>
<a href="card_templates.php">My Templates</a>
<a href="card_portfolio.php">Portfolio</a>
<a href="card_availability.php">Availability</a>
<a href="card_bookings.php">Bookings</a>
<a href="card_earnings.php">Earnings</a>
<a href="card_reviews.php">Reviews</a>
<a href="../auth/logout.php" class="text-danger">Logout</a>

</div>

<!-- MAIN CONTENT -->

<div class="col-md-10 p-0">

<div class="topbar d-flex justify-content-between align-items-center">

<h5 class="mb-0">
Welcome, <?php echo htmlspecialchars($business_name); ?>
</h5>

<span class="badge text-uppercase"
style="background:linear-gradient(135deg,#4e73df,#1cc88a);">

<?php echo htmlspecialchars($vendor_type); ?>

</span>

</div>

<div class="container mt-4">

<div class="card shadow-sm p-4 card-box">

<h4 class="mb-4">Upload Invitation Template</h4>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label>Template Name</label>
<input type="text" name="template_name" class="form-control" required>
</div>

<div class="mb-3">

<label>Select Event Type</label>

<select name="event_id" class="form-control" required>

<option value="">Select Event</option>

<?php

$events = $conn->query("SELECT * FROM events");

while($row = $events->fetch_assoc()){

echo "<option value='".$row['id']."'>".$row['event_name']."</option>";

}

?>

</select>

</div>

<div class="mb-3">

<label>Price (₹)</label>

<input type="number" name="price" class="form-control" required>

</div>

<div class="mb-3">

<label>Upload Template Image</label>

<input type="file" name="image" class="form-control" accept="image/*" required>

</div>

<button type="submit" name="upload" class="btn btn-primary">

Upload Template

</button>

</form>

</div>

</div>

</div>

</div>
</div>

</body>
</html>