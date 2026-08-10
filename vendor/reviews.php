<?php 
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['vendor_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* ---------------- TOTAL REVIEWS (FROM BOOKINGS) ---------------- */

$stmt = $conn->prepare("
    SELECT 
        COUNT(rating) as total, 
        AVG(rating) as avg_rating 
    FROM bookings 
    WHERE vendor_id=? 
    AND rating IS NOT NULL 
    AND review IS NOT NULL
");

$stmt->bind_param("i", $vendor_id);

$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

$total_reviews = $data['total'] ?? 0;

$average_rating = round($data['avg_rating'], 1);

if($average_rating == null){
    $average_rating = 0;
}

/* ---------------- FETCH REVIEWS (FROM BOOKINGS) ---------------- */

$stmt = $conn->prepare("
    SELECT rating, review
    FROM bookings 
    WHERE vendor_id=? 
    AND rating IS NOT NULL 
    AND review IS NOT NULL
");

$stmt->bind_param("i", $vendor_id);

$stmt->execute();

$reviews = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

<title>Vendor Reviews</title>

<link href="../assets/css/bootstrap.min.css" rel="stylesheet">

<style>

/* ======= YOUR ORIGINAL DESIGN (UNCHANGED) ======= */

body{
    background:linear-gradient(135deg,#eef2ff,#f8fbff);
    font-family:'Segoe UI',sans-serif;
}

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

.topbar{
    background:white;
    padding:18px 25px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.review-banner{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    color:white;
    border-radius:22px;
    padding:35px;
    box-shadow:0 8px 22px rgba(0,0,0,0.08);
}

.review-banner h2{
    font-weight:bold;
}

.summary-card{
    background:white;
    border-radius:20px;
    padding:25px;
    text-align:center;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
    transition:0.3s;
}

.summary-card:hover{
    transform:translateY(-5px);
}

.icon-box{
    width:60px;
    height:60px;
    border-radius:15px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    margin-bottom:15px;
    font-size:25px;
}

.icon-purple{background:#efe8ff;color:#764ba2;}
.icon-gold{background:#fff6dd;color:#ffb300;}

.review-card{
    background:white;
    border-radius:22px;
    padding:30px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

.single-review{
    background:#faf7ff;
    border:1px solid #f0e8ff;
    border-radius:18px;
    padding:20px;
    margin-bottom:20px;
    transition:0.3s;
}

.single-review:hover{
    transform:translateY(-3px);
}

.star{
    color:#ffc107;
    font-size:18px;
}

.review-date{
    color:#7b8190;
    font-size:14px;
}

.empty-review{
    text-align:center;
    padding:25px;
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
<a href="bookings.php">Bookings</a>
<a href="earnings.php">Earnings</a>
<a href="reviews.php" class="active">Reviews</a>

<a href="../auth/logout.php" class="text-warning">
Logout
</a>

</div>

<!-- MAIN -->
<div class="col-md-10 p-0">

<div class="topbar">
<h5 class="mb-0">Customer Reviews</h5>
</div>

<div class="container mt-4">

<div class="review-banner mb-4">
<h2>Customer Feedback ⭐</h2>
<p class="mt-3 mb-0">
Check customer ratings and reviews to improve your event services and business quality.
</p>
</div>

<div class="row g-4 mb-4">

<div class="col-md-6">
<div class="summary-card">
<div class="icon-box icon-purple">💬</div>
<h6>Total Reviews</h6>
<h3 class="fw-bold"><?php echo $total_reviews; ?></h3>
<small class="text-muted">Customer feedback received</small>
</div>
</div>

<div class="col-md-6">
<div class="summary-card">
<div class="icon-box icon-gold">⭐</div>
<h6>Average Rating</h6>
<h3 class="fw-bold"><?php echo $average_rating; ?> / 5</h3>
<small class="text-muted">Overall customer satisfaction</small>
</div>
</div>

</div>

<div class="review-card">

<h5 class="mb-4">All Customer Reviews</h5>

<?php if($reviews->num_rows > 0): ?>

<?php while($row = $reviews->fetch_assoc()): ?>

<div class="single-review">

<!-- STARS -->
<div class="mb-2">

<?php
for($i=1; $i<=5; $i++){
    if($i <= $row['rating']){
        echo '<span class="star">★</span>';
    } else {
        echo '<span class="star text-secondary">★</span>';
    }
}
?>

</div>

<!-- REVIEW TEXT -->
<p class="mb-2">
<?php echo htmlspecialchars($row['review']); ?>
</p>



</div>

<?php endwhile; ?>

<?php else: ?>

<div class="empty-review">
No customer reviews available yet.
</div>

<?php endif; ?>

</div>

</div>

</div>

</div>
</div>

</body>
</html>