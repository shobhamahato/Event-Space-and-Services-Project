<?php
session_start();
require_once("../config/db.php");

/* CHECK LOGIN */
if(!isset($_SESSION['vendor_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* FETCH VENDOR INFO */
$stmt = $conn->prepare("SELECT business_name, vendor_type FROM vendors WHERE vendor_id=?");
$stmt->bind_param("i",$vendor_id);
$stmt->execute();
$vendor = $stmt->get_result()->fetch_assoc();

$business_name = $vendor['business_name'];
$vendor_type = $vendor['vendor_type'];

/* DELETE TEMPLATE */

if(isset($_GET['delete'])){

$template_id = $_GET['delete'];

$stmt = $conn->prepare("DELETE FROM templates WHERE id=? AND vendor_id=?");
$stmt->bind_param("ii",$template_id,$vendor_id);
$stmt->execute();

echo "<script>
alert('Template Deleted Successfully');
window.location='card_templates.php';
</script>";

}

/* FETCH TEMPLATES */

$query = $conn->prepare("
SELECT templates.*, events.event_name
FROM templates
LEFT JOIN events ON templates.event_id = events.id
WHERE templates.vendor_id=?
ORDER BY templates.id DESC
");

$query->bind_param("i",$vendor_id);
$query->execute();
$templates = $query->get_result();

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>My Templates</title>
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

.template-card{
border-radius:15px;
overflow:hidden;
transition:0.3s;
}

.template-card:hover{
transform:translateY(-6px);
box-shadow:0 15px 35px rgba(0,0,0,0.2);
}

.template-img{
height:220px;
object-fit:cover;
}

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
<a href="card_services.php">Upload Templates</a>
<a href="card_templates.php" class="active">My Templates</a>
<a href="card_portfolio.php">Portfolio</a>
<!-- <a href="card_availability.php">Availability</a> -->
<a href="card_bookings.php">Bookings</a>
<a href="card_earnings.php">Earnings</a>
<a href="card_reviews.php">Reviews</a>
<a href="../auth/logout.php" class="text-danger">Logout</a>

</div>

<!-- MAIN CONTENT -->

<div class="col-md-10 p-0">

<!-- TOPBAR -->

<div class="topbar d-flex justify-content-between align-items-center">

<h5 class="mb-0">
Welcome, <?php echo htmlspecialchars($business_name); ?>
</h5>

<span class="badge text-uppercase"
style="background:linear-gradient(135deg,#4e73df,#1cc88a);">

<?php echo htmlspecialchars($vendor_type); ?>

</span>

</div>

<!-- PAGE CONTENT -->

<div class="container mt-4">

<h4 class="mb-4">My Invitation Templates</h4>

<div class="row">

<?php

if($templates->num_rows > 0){

while($row = $templates->fetch_assoc()){

?>

<div class="col-md-4 mb-4">

<div class="card template-card shadow-sm">

<img src="../uploads/templates/<?php echo $row['image']; ?>"
class="card-img-top template-img">

<div class="card-body">

<h5 class="card-title">
<?php echo htmlspecialchars($row['template_name']); ?>
</h5>

<p class="text-muted mb-1">
Event: <?php echo htmlspecialchars($row['event_name']); ?>
</p>

<p class="fw-bold text-success">
₹<?php echo number_format($row['price'],2); ?>
</p>

<a href="?delete=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this template?')">

Delete

</a>

</div>

</div>

</div>

<?php

}

}else{

echo "<p>No templates uploaded yet.</p>";

}

?>

</div>

</div>

</div>

</div>
</div>

</body>
</html>