<?php  

session_start();

$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* USER LOGIN CHECK */

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* USER DATA */

$userQuery = mysqli_query($conn,
"SELECT * FROM users WHERE id='$user_id'");

$userData = mysqli_fetch_assoc($userQuery);

$userName = $userData['name'];

/* SAVE REVIEW */

if(isset($_POST['submit_review'])){

    $booking_id = $_POST['booking_id'];

    $rating = $_POST['rating'];

    $review = mysqli_real_escape_string($conn,$_POST['review']);

    mysqli_query($conn,"
    UPDATE bookings 
    SET rating='$rating',
        review='$review'
    WHERE id='$booking_id'
    AND user_id='$user_id'
    ");
}

/* WISHLIST COUNT */

$wishlistCount = 0;

$wishlistQuery = mysqli_query($conn,
"SELECT * FROM wishlist WHERE user_id='$user_id'");

if($wishlistQuery){
    $wishlistCount = mysqli_num_rows($wishlistQuery);
}

/* CART COUNT */

$cartCount = 0;

$cartQuery = mysqli_query($conn,
"SELECT * FROM cart WHERE user_id='$user_id'");

if($cartQuery){
    $cartCount = mysqli_num_rows($cartQuery);
}

/* FETCH BOOKINGS */

$query = mysqli_query($conn,

"SELECT bookings.*, 
vendors.business_name, 
services.picture

FROM bookings

LEFT JOIN vendors 
ON bookings.vendor_id = vendors.vendor_id

LEFT JOIN services
ON bookings.service_id = services.service_id

WHERE bookings.user_id='$user_id'

ORDER BY bookings.id DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Orders</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:
    linear-gradient(135deg,#ffdde1,#cdb4db);
    font-family:'Poppins',sans-serif;
    overflow-x:hidden;
    padding-bottom:120px;
    min-height:100vh;
}

/* ================= NAVBAR ================= */

.navbar{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(18px);
    padding:12px 35px;
    width:100%;
    box-shadow:
    0 4px 20px rgba(181,126,220,0.15);
    position:sticky;
    top:0;
    z-index:999;
}

.navbar-brand{
    color:#b85fc6;
    font-size:28px;
    font-weight:700;
    text-decoration:none;
}

.logo-icon{
    color:#ff8fab;
}

.nav-link-icon{
    text-decoration:none;
    position:relative;
}

.nav-icon{
    color:#555;
    font-size:21px;
    transition:0.3s;
}

.nav-icon:hover{
    color:#b85fc6;
    transform:translateY(-2px) scale(1.08);
}

.icon-badge{
    position:absolute;
    top:-8px;
    right:-10px;
    background:#b85fc6;
    color:white;
    min-width:20px;
    height:20px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
    font-weight:700;
}

.profile{
    display:flex;
    align-items:center;
    gap:12px;
    background:white;
    padding:7px 14px;
    border-radius:50px;
    transition:0.3s;
    cursor:pointer;
}

.profile:hover{
    transform:translateY(-2px);
}

.profile img{
    width:42px;
    height:42px;
    border-radius:50%;
    border:3px solid #d8b4f8;
}

.profile span{
    font-weight:600;
    color:#444;
}

.profile-menu{
    border:none;
    border-radius:20px;
    padding:10px;
    min-width:220px;
    box-shadow:0 15px 35px rgba(0,0,0,0.08);
}

.profile-menu .dropdown-item{
    padding:12px;
    border-radius:12px;
    transition:0.3s;
}

.profile-menu .dropdown-item:hover{
    background:#f3e8ff;
}

/* ================= PAGE TITLE ================= */

.page-title-box{
    background:rgba(255,255,255,0.78);
    backdrop-filter:blur(12px);
    border-radius:28px;
    padding:28px 35px;
    margin-top:30px;
    margin-bottom:30px;
    box-shadow:0 10px 35px rgba(0,0,0,0.08);
}

.page-title{
    font-size:38px;
    font-weight:700;
    color:#2b2d42;
}

.page-subtitle{
    color:#666;
    margin-top:5px;
}

/* ================= ORDER CARD ================= */

.order-card{
    background:rgba(255,255,255,0.9);
    backdrop-filter:blur(12px);
    border-radius:28px;
    overflow:hidden;
    margin-bottom:24px;
    box-shadow:
    0 12px 35px rgba(0,0,0,0.08);
    transition:0.35s;
}

.order-card:hover{
    transform:translateY(-5px);
    box-shadow:
    0 20px 45px rgba(181,126,220,0.18);
}

.order-wrapper{
    display:flex;
    align-items:center;
    gap:20px;
    padding:20px;
}

.order-image{
    width:220px;
    height:160px;
    border-radius:24px;
    object-fit:cover;
    flex-shrink:0;
}

.order-content{
    flex:1;
}

.vendor-name{
    font-size:26px;
    font-weight:700;
    color:#2b2d42;
    margin-bottom:5px;
}

.service-name{
    font-size:15px;
    color:#7c3aed;
    font-weight:600;
    margin-bottom:15px;
}

.order-info{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    margin-bottom:16px;
}

.info-pill{
    background:#f5ecff;
    color:#5a189a;
    padding:10px 16px;
    border-radius:50px;
    font-size:13px;
    font-weight:600;
}

.price{
    font-size:30px;
    font-weight:700;
    color:#16a34a;
    margin-top:8px;
}

/* ================= STATUS ================= */

.badge-status{
    padding:10px 18px;
    border-radius:50px;
    font-size:13px;
    font-weight:700;
}

.confirmed{
    background:#dcfce7;
    color:#15803d;
}

.pending{
    background:#fef3c7;
    color:#d97706;
}

.cancelled{
    background:#fee2e2;
    color:#dc2626;
}

.badge-payment{
    background:#dbeafe;
    color:#1d4ed8;
    padding:10px 18px;
    border-radius:50px;
    font-size:13px;
    font-weight:700;
}

/* ================= REVIEW SECTION ================= */

.review-section{
    width:360px;
    background:#faf5ff;
    border-radius:22px;
    padding:20px;
    flex-shrink:0;
}

.review-title{
    font-size:16px;
    font-weight:700;
    color:#5b21b6;
    margin-bottom:12px;
}

.star-rating{
    direction:rtl;
    display:flex;
    justify-content:flex-end;
    gap:4px;
    margin-bottom:14px;
}

.star-rating input{
    display:none;
}

.star-rating label{
    font-size:22px;
    color:#d1d5db;
    cursor:pointer;
    transition:0.3s;
}

.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label{
    color:#facc15;
}

.review-box{
    border:none;
    background:white;
    border-radius:16px;
    padding:14px;
    resize:none;
    width:100%;
    outline:none;
    font-size:14px;
    min-height:90px;
}

.review-box:focus{
    box-shadow:0 0 0 4px rgba(199,125,255,0.18);
}

.review-btn{
    margin-top:12px;
    background:
    linear-gradient(135deg,#ff8fab,#b185db);
    border:none;
    color:white;
    padding:12px 20px;
    border-radius:14px;
    width:100%;
    font-weight:600;
    transition:0.3s;
}

.review-btn:hover{
    transform:translateY(-2px);
}

/* ================= BUTTON ================= */

.pay-btn{
    background:
    linear-gradient(135deg,#22c55e,#16a34a);
    color:white;
    padding:10px 18px;
    border-radius:50px;
    text-decoration:none;
    font-size:13px;
    font-weight:700;
    display:inline-block;
    transition:0.3s;
}

.pay-btn:hover{
    color:white;
    transform:translateY(-2px);
}

/* ================= EMPTY ================= */

.empty-box{
    background:rgba(255,255,255,0.9);
    border-radius:30px;
    padding:70px 20px;
    text-align:center;
}

.empty-box i{
    font-size:70px;
    color:#d8b4f8;
    margin-bottom:20px;
}

.empty-box h3{
    font-weight:700;
    color:#2b2d42;
}

.empty-box p{
    color:#666;
}

/* ================= FOOTER ================= */

.footer{
    background:rgba(255,255,255,0.82);
    backdrop-filter:blur(18px);
    margin-top:60px;
    padding:20px;
    text-align:center;
    color:#555;
    font-weight:500;
    border-top:1px solid rgba(255,255,255,0.3);
}
/* ================= BOTTOM NAV ================= */

.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;

    width:100%;

    background:rgba(255,255,255,0.82);

    backdrop-filter:blur(18px);

    padding:16px 0;

    box-shadow:
    0 -5px 25px rgba(181,126,220,0.15);

    z-index:999;

    display:flex;
    justify-content:space-around;
}

.bottom-link{
    text-decoration:none;
    color:#555;
    text-align:center;
    font-size:13px;
    transition:0.3s;
}

.bottom-link:hover{
    color:#b85fc6;
}

.bottom-link i{
    font-size:22px;
}


/* ================= MOBILE ================= */

@media(max-width:992px){

.order-wrapper{
    flex-direction:column;
}

.review-section{
    width:100%;
}

.order-image{
    width:100%;
    height:220px;
}

}


@media(max-width:768px){

.page-title{
    font-size:28px;
}

.vendor-name{
    font-size:22px;
}

.price{
    font-size:24px;
}

.navbar{
    padding:12px 18px;
}

}

</style>

</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="navbar d-flex justify-content-between align-items-center">

    <a class="navbar-brand" href="dashboard.php">

        <i class="fa fa-calendar-check me-2 logo-icon"></i>

        EventSpace

    </a>

    <div class="d-flex align-items-center gap-4">

        <!-- Wishlist -->
        <a href="wishlist.php"
           class="nav-link-icon">

            <i class="bi bi-heart nav-icon"></i>

            <?php if($wishlistCount > 0){ ?>

            <span class="icon-badge">
                <?php echo $wishlistCount; ?>
            </span>

            <?php } ?>

        </a>

        <!-- Cart -->
        <a href="cart.php"
           class="nav-link-icon">

            <i class="bi bi-cart3 nav-icon"></i>

            <?php if($cartCount > 0){ ?>

            <span class="icon-badge">
                <?php echo $cartCount; ?>
            </span>

            <?php } ?>

        </a>

        <!-- Profile -->
        <div class="dropdown">

            <div class="profile dropdown-toggle"
                 data-bs-toggle="dropdown">

                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=ff4f9a&color=fff">

                <span>
                    <?php echo htmlspecialchars($userName); ?>
                </span>

            </div>

            <ul class="dropdown-menu dropdown-menu-end profile-menu">

                <li>
                    <a class="dropdown-item"
                       href="profile.php">

                        <i class="bi bi-person-circle me-2"></i>

                        Profile

                    </a>
                </li>

                <li>
                    <a class="dropdown-item"
                       href="dashboard.php">

                        <i class="bi bi-house-door me-2"></i>

                        Dashboard

                    </a>
                </li>

                <li><hr></li>

                <li>
                    <a class="dropdown-item text-danger"
                       href="../auth/logout.php">

                        <i class="bi bi-box-arrow-right me-2"></i>

                        Logout

                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>

<!-- ================= PAGE TITLE ================= -->

<div class="container">

    <div class="page-title-box">

        <h1 class="page-title">
            My Booking History
        </h1>

        <p class="page-subtitle">
            Track your bookings, payments and reviews easily.
        </p>

    </div>

</div>

<!-- ================= BOOKINGS ================= -->

<div class="container">

<?php if(mysqli_num_rows($query) > 0){ ?>

<?php while($row=mysqli_fetch_assoc($query)){ ?>

<div class="order-card">

<div class="order-wrapper">

<!-- IMAGE -->

<?php 

if(!empty($row['picture'])){

$image = "../uploads/services/".$row['picture'];

}else{

$image = "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1200&auto=format&fit=crop";
}

?>

<img src="<?php echo $image; ?>" class="order-image">

<!-- CONTENT -->

<div class="order-content">

    <h2 class="vendor-name">

        <?php echo htmlspecialchars($row['business_name']); ?>

    </h2>

    <div class="service-name">

        <?php echo htmlspecialchars($row['service_name']); ?>

    </div>

    <div class="order-info">

        <div class="info-pill">
            <i class="fa fa-calendar me-2"></i>

            <?php echo date("d M Y", strtotime($row['event_date'])); ?>
        </div>

        <div class="info-pill">
            <i class="fa fa-clock me-2"></i>

            <?php echo htmlspecialchars($row['event_time']); ?>
        </div>

        <div class="info-pill">
            <i class="fa fa-users me-2"></i>

            <?php echo htmlspecialchars($row['guest_count']); ?> Guests
        </div>

    </div>

    <div class="d-flex flex-wrap align-items-center gap-3">
        <strong class="text-dark">
            Booking Request :
        </strong>
        <?php 

        $status = strtolower($row['booking_status']);

        ?>

        <span class="badge-status <?php echo $status; ?>">

            <?php echo ucfirst($row['booking_status']); ?>

        </span>

        <?php 

        $booking_status = strtolower($row['booking_status']);?>
        <!-- payment status -->
          <div class="d-flex align-items-center gap-3 flex-wrap">

        <strong class="text-dark">
            Payment :
        </strong>
        <?php
        $payment_status = strtolower($row['payment_status']);

        if($booking_status == "confirmed" && $payment_status == "pending"){ 
        ?>

        <a href="payment.php?booking_id=<?php echo $row['id']; ?>" 
        class="pay-btn">

            Pay Now

        </a>

        <?php } else { ?>

        <span class="badge-payment">

            <?php echo ucfirst($row['payment_status']); ?>

        </span>

        <?php } ?>
</div>
    </div>

    <div class="price">

        ₹<?php echo number_format($row['amount'],2); ?>

    </div>

</div>

<!-- REVIEW SECTION -->

<!-- REVIEW SECTION -->

<div class="review-section">

<?php

$booking_status = strtolower($row['booking_status']);
$payment_status = strtolower($row['payment_status']);

if($payment_status == "paid" && $booking_status == "confirmed"){

    // CHECK REVIEW ALREADY SUBMITTED
    if(!empty($row['rating']) || !empty($row['review'])){
?>

<!-- REVIEW SUBMITTED -->

<div class="d-flex flex-column justify-content-center align-items-center h-100 text-center">

    <div style="
        width:70px;
        height:70px;
        border-radius:50%;
        background:#dcfce7;
        color:#16a34a;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:30px;
    ">
        <i class="fa-solid fa-check"></i>
    </div>

    <h5 class="fw-bold mt-3 text-success">
        Review Submitted
    </h5>

    <p class="text-muted mb-3">
        Thank you for your valuable feedback.
    </p>

    <!-- SHOW STARS -->

    <div class="mb-2">

        <?php

        for($i=1; $i<=5; $i++){

            if($i <= $row['rating']){
                echo '<i class="fa-solid fa-star text-warning"></i>';
            }else{
                echo '<i class="fa-regular fa-star text-warning"></i>';
            }

        }

        ?>

    </div>

    <!-- SHOW REVIEW -->

    <?php if(!empty($row['review'])){ ?>

    <div style="
        background:white;
        padding:14px;
        border-radius:16px;
        width:100%;
        font-size:14px;
        color:#555;
    ">

        "<?php echo htmlspecialchars($row['review']); ?>"

    </div>

    <?php } ?>

</div>

<?php } else { ?>

<!-- REVIEW FORM -->

<div class="review-title">
    Ratings & Review
</div>

<form method="POST">

    <input type="hidden"
           name="booking_id"
           value="<?php echo $row['id']; ?>">

    <div class="star-rating">

        <input type="radio"
               name="rating"
               value="5"
               id="5_<?php echo $row['id']; ?>"
               required>

        <label for="5_<?php echo $row['id']; ?>">
            <i class="fa-solid fa-star"></i>
        </label>

        <input type="radio"
               name="rating"
               value="4"
               id="4_<?php echo $row['id']; ?>">

        <label for="4_<?php echo $row['id']; ?>">
            <i class="fa-solid fa-star"></i>
        </label>

        <input type="radio"
               name="rating"
               value="3"
               id="3_<?php echo $row['id']; ?>">

        <label for="3_<?php echo $row['id']; ?>">
            <i class="fa-solid fa-star"></i>
        </label>

        <input type="radio"
               name="rating"
               value="2"
               id="2_<?php echo $row['id']; ?>">

        <label for="2_<?php echo $row['id']; ?>">
            <i class="fa-solid fa-star"></i>
        </label>

        <input type="radio"
               name="rating"
               value="1"
               id="1_<?php echo $row['id']; ?>">

        <label for="1_<?php echo $row['id']; ?>">
            <i class="fa-solid fa-star"></i>
        </label>

    </div>

    <textarea
        name="review"
        class="review-box"
        placeholder="Write your feedback..."
        required></textarea>

    <button type="submit"
            name="submit_review"
            class="review-btn">

        Submit Review

    </button>

</form>

<?php } ?>

<?php } else { ?>

<!-- REVIEW LOCKED -->

<div class="d-flex flex-column justify-content-center h-100">

    <h6 class="fw-bold text-dark mb-2">
        Review Locked
    </h6>

    <p class="text-muted mb-0">
        Complete payment and confirmation to submit your review.
    </p>

</div>

<?php } ?>

</div>

</div>

</div>

<?php } ?>

<?php } else { ?>

<div class="empty-box">

    <i class="fa-regular fa-calendar-xmark"></i>

    <h3>No Bookings Yet</h3>

    <p>
        You have not booked any vendors yet.
    </p>

    <a href="dashboard.php"
       class="btn btn-lg mt-3 text-white"
       style="background:linear-gradient(135deg,#ff8fab,#b185db); border:none; border-radius:14px; padding:12px 28px;">

        Explore Vendors

    </a>

</div>

<?php } ?>

</div>

<!-- ================= FOOTER ================= -->

<!-- ================= BOTTOM NAV ================= -->

<div class="bottom-nav">

    <a href="dashboard.php"
       class="bottom-link">

        <i class="fa fa-home"></i>
        <br>

        Home

    </a>

    <a href="vendors.php"
       class="bottom-link">

        <i class="fa fa-th-large"></i>
        <br>

        Vendors

    </a>

    <a href="wishlist.php"
       class="bottom-link">

        <i class="bi bi-heart"></i>
        <br>

        Wishlist

    </a>

    <a href="cart.php"
       class="bottom-link">

        <i class="bi bi-cart"></i>
        <br>

        Cart

    </a>
        <a href="orders.php" style="text-decoration:none; color:inherit;">
            <i class="bi bi-box"></i>
            <br>
        Bookings
        </a>
    <a href="profile.php"
       class="bottom-link">

        <i class="bi bi-person"></i>
        <br>

        Profile

    </a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>