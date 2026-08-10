<?php
session_start();

/* CHECK LOGIN */
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

/* DATABASE CONNECTION */
$conn = new mysqli("localhost", "root", "", "event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* GET VENDOR ID */
if(!isset($_GET['vendor_id'])){
    header("Location: vendors.php");
    exit();
}

$vendor_id = $_GET['vendor_id'];

/* FETCH USER */
$user_id = $_SESSION['user_id'];

$userQuery = "SELECT * FROM users WHERE id='$user_id'";
$userResult = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_assoc($userResult);

$userName = $userData['name'];

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

/* FETCH VENDOR */
$sql = "SELECT * FROM vendors
        WHERE vendor_id='$vendor_id'
        AND status='approved'";

$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
    echo "Vendor not found";
    exit();
}

$vendor = mysqli_fetch_assoc($result);

$type = $vendor['vendor_type'];

/* DYNAMIC TABLE FETCH */

$detailQuery = "";

if($type == "decorator"){
    $detailQuery = "SELECT * FROM decorators WHERE vendor_id='$vendor_id'";
}
elseif($type == "beauty_parlour"){
    $detailQuery = "SELECT * FROM beauty_parlours WHERE vendor_id='$vendor_id'";
}
else if($type == "cards" || $type == "card_vendor"){
    $detailQuery = "SELECT * FROM cards WHERE vendor_id='$vendor_id'";
}
elseif($type == "caterer"){
    $detailQuery = "SELECT * FROM caterers WHERE vendor_id='$vendor_id'";
}
elseif($type == "venue"){
    $detailQuery = "SELECT * FROM venues WHERE vendor_id='$vendor_id'";
}
elseif($type == "music_dj"){
    $detailQuery = "SELECT * FROM music_vendors WHERE vendor_id='$vendor_id'";
}
elseif($type == "photography"){
    $detailQuery = "SELECT * FROM photography_vendors WHERE vendor_id='$vendor_id'";
}

$detailResult = mysqli_query($conn, $detailQuery);

if(!$detailResult){
    die("Query Failed : " . mysqli_error($conn));
}

$details = mysqli_fetch_assoc($detailResult);

/* FETCH PORTFOLIO */
$portfolioQuery = "SELECT * FROM vendor_portfolio
                   WHERE vendor_id='$vendor_id'";

$portfolioResult = mysqli_query($conn, $portfolioQuery);

/* FETCH SERVICES */
$servicesQuery = "SELECT * FROM services WHERE vendor_id='$vendor_id'";
$servicesResult = mysqli_query($conn, $servicesQuery);

/* FETCH PACKAGES */
$packagesQuery = "SELECT * FROM packages WHERE vendor_id='$vendor_id'";
$packagesResult = mysqli_query($conn, $packagesQuery);

/* MAIN IMAGE */
if(mysqli_num_rows($portfolioResult) > 0){

    $firstImage = mysqli_fetch_assoc($portfolioResult);

    $mainImage = "../uploads/portfolio/" . $firstImage['image_path'];

}else{

    $mainImage = "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1200";
}
/* =========================
   FETCH REAL RATINGS
========================= */

$ratingQuery = mysqli_query($conn, "

SELECT 
    AVG(rating) AS avg_rating,
    COUNT(rating) AS total_reviews
FROM bookings
WHERE vendor_id='$vendor_id'
AND rating IS NOT NULL
AND rating > 0

");

$ratingData = mysqli_fetch_assoc($ratingQuery);

$avgRating = round($ratingData['avg_rating'], 1);

$totalReviews = $ratingData['total_reviews'];

/* DEFAULT VALUES */

if(empty($avgRating)){
    $avgRating = 0;
}

if(empty($totalReviews)){
    $totalReviews = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Vendor Details</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:
linear-gradient(135deg, #ffcccc, #cdb4db);


    font-family:'Poppins',sans-serif;

    padding-bottom:120px;

    overflow-x:hidden;

    color:#2d2d2d;
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


/* ================= HERO ================= */

.hero-section{
    background:rgba(255,255,255,0.78);

    backdrop-filter:blur(16px);

    border-radius:28px;

    padding:30px;

    box-shadow:
    0 15px 40px rgba(168,139,250,0.15);
}

.hero-image{
    width:100%;
    height:430px;
    object-fit:cover;
    border-radius:24px;
}

.back-btn{
    border:2px solid #a78bfa;
    color:#7c3aed;
    text-decoration:none;
    padding:10px 18px;
    border-radius:50px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    transition:0.3s;
    font-weight:600;
}

.back-btn:hover{
    background:#a78bfa;
    color:white;
}

.vendor-badge{
    background:
    linear-gradient(135deg,#a78bfa,#f9a8d4);

    color:white;

    padding:8px 18px;

    border-radius:50px;

    display:inline-block;

    font-weight:600;
}

.vendor-title{
    font-size:42px;
    font-weight:700;
    color:#4c1d95;
    margin-top:14px;
}

.vendor-subtitle{
    color:#6b7280;
    margin-top:10px;
    font-size:16px;
}

.rating i{
    color:#fbbf24;
}

/* ================= QUICK LINKS ================= */

.quick-links{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    margin-top:25px;
}

.quick-links a{
    background:
    linear-gradient(135deg,#a78bfa,#f9a8d4);

    color:white;

    padding:12px 20px;

    border-radius:14px;

    text-decoration:none;

    font-weight:500;

    transition:0.3s;
}

.quick-links a:hover{
    transform:translateY(-2px);
}

/* ================= INFO GRID ================= */

.info-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:16px;
    margin-top:25px;
}

.info-card{
    background:#faf5ff;

    padding:18px;

    border-radius:18px;

    border:1px solid #f3e8ff;
}

.info-card i{
    color:#a855f7;
    font-size:18px;
    margin-bottom:10px;
}

.info-card h6{
    color:#6b7280;
    font-size:14px;
}

.info-card p{
    margin:0;
    font-weight:600;
}

/* ================= SECTION ================= */

.section-box{
    background:rgba(255,255,255,0.78);

    backdrop-filter:blur(14px);

    border-radius:28px;

    padding:30px;

    margin-top:30px;

    box-shadow:
    0 12px 35px rgba(168,139,250,0.14);
}

.section-title{
    font-size:30px;
    font-weight:700;
    color:#5b21b6;
    margin-bottom:25px;
}

/* ================= DETAIL ITEM ================= */

.detail-item{
    background:#faf5ff;

    padding:16px;

    border-radius:16px;

    margin-bottom:14px;

    border:1px solid #f3e8ff;
}

/* ================= SERVICE CARD ================= */

.service-card{
    background:white;

    border-radius:24px;

    overflow:hidden;

    box-shadow:
    0 10px 30px rgba(168,139,250,0.12);

    transition:0.4s;

    position:relative;

    height:100%;
}

.service-card:hover{
    transform:translateY(-8px);
}

.service-image{
    width:100%;
    height:240px;
    object-fit:cover;
}

.service-body{
    padding:22px;
}

.service-title{
    font-size:24px;
    font-weight:700;
    color:#4c1d95;
}

.service-price{
    color:#10b981;
    font-size:24px;
    font-weight:700;
    margin:12px 0;
}

.service-desc{
    color:#6b7280;
    min-height:65px;
}

.wishlist-btn{
    position:absolute;
    top:18px;
    right:18px;

    width:42px;
    height:42px;

    border-radius:50%;

    background:white;

    display:flex;
    align-items:center;
    justify-content:center;

    box-shadow:0 5px 15px rgba(0,0,0,0.15);

    cursor:pointer;
}

.wishlist-btn i{
    color:#ef4444;
}

.btn-theme{
    background:
    linear-gradient(135deg,#a78bfa,#f9a8d4);

    border:none;

    color:white;

    border-radius:14px;

    padding:12px;

    width:100%;

    font-weight:600;

    transition:0.3s;
}

.btn-theme:hover{
    color:white;
    transform:translateY(-2px);
}

/* ================= GALLERY ================= */

.gallery img{
    width:100%;
    height:260px;
    object-fit:cover;

    border-radius:22px;

    transition:0.4s;
}

.gallery img:hover{
    transform:scale(1.03);
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

@media(max-width:768px){

    .hero-image{
        height:260px;
    }

    .vendor-title{
        font-size:30px;
    }

    .navbar{
        padding:12px 18px;
    }

    .quick-links{
        gap:10px;
    }

    .quick-links a{
        width:100%;
        text-align:center;
    }

    .bottom-link{
        font-size:10px;
    }

    .bottom-link i{
        font-size:18px;
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
                       href="wishlist.php">

                        <i class="bi bi-heart me-2"></i>

                        Wishlist

                    </a>
                </li>

                <li>
                    <a class="dropdown-item"
                       href="cart.php">

                        <i class="bi bi-cart me-2"></i>

                        Cart

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

<!-- HERO SECTION -->

<div class="container mt-4">

    <div class="hero-section">

        <div class="row align-items-center g-4">

            <div class="col-lg-6">

                <img src="<?php echo $mainImage; ?>"
                     class="hero-image">

            </div>

            <div class="col-lg-6">

                <a href="vendors.php"
                   class="back-btn mb-4">

                    <i class="fa fa-arrow-left"></i>

                    Back

                </a>

                <div class="vendor-badge mb-3">

                    <?php echo ucfirst(str_replace("_"," ",$type)); ?>

                </div>

                <h1 class="vendor-title">

                    <?php echo $vendor['business_name']; ?>

                </h1>

                <p class="vendor-subtitle">

                    Professional event services for your dream celebration.

                </p>

                <div class="rating mb-4">

<?php

$fullStars = floor($avgRating);

$halfStar = ($avgRating - $fullStars) >= 0.5;

$emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

/* FULL STARS */
for($i=0; $i<$fullStars; $i++){

    echo '<i class="fa fa-star"></i>';

}

/* HALF STAR */
if($halfStar){

    echo '<i class="fa fa-star-half-alt"></i>';

}

/* EMPTY STARS */
for($i=0; $i<$emptyStars; $i++){

    echo '<i class="fa fa-star text-secondary"></i>';

}

?>
</div>

                <div class="quick-links">

                    <a href="#vendor-details">

                        <i class="fa fa-circle-info me-2"></i>

                        Vendor Details

                    </a>

                    <a href="#services">

                        <i class="fa fa-briefcase me-2"></i>

                        Services

                    </a>

                    <a href="#packages">

                        <i class="fa fa-box-open me-2"></i>

                        Packages

                    </a>

                    <a href="#portfolio">

                        <i class="fa fa-image me-2"></i>

                        Portfolio

                    </a>

                </div>

                <div class="info-grid">

                    <div class="info-card">

                        <i class="fa fa-user"></i>

                        <h6>Owner Name</h6>

                        <p><?php echo $vendor['owner_name']; ?></p>

                    </div>

                    <div class="info-card">

                        <i class="fa fa-phone"></i>

                        <h6>Phone</h6>

                        <p><?php echo $vendor['phone']; ?></p>

                    </div>

                    <div class="info-card">

                        <i class="fa fa-location-dot"></i>

                        <h6>Location</h6>

                        <p>

                            <?php echo !empty($details['city']) ? $details['city'] : "Not Available"; ?>

                        </p>

                    </div>

                    <div class="info-card">

                        <i class="fa fa-award"></i>

                        <h6>Experience</h6>

                        <p>

                            <?php echo !empty($details['experience']) ? $details['experience']." Years" : "N/A"; ?>

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- VENDOR DETAILS -->
<!-- VENDOR DETAILS -->

<div class="container">

    <div class="section-box"
         id="vendor-details">

        <h2 class="section-title">

            Vendor Details

        </h2>

<?php

/* FIELDS TO HIDE */

$hideFields = [

    "id",
    "vendor_id",
    "street",
    "city",
    "pincode",
    "packages",
    "starting_price",
    "portfolio_images",
    "created_at"

];

foreach($details as $key => $value){

/* SKIP EMPTY VALUES */
if(empty($value)){
    continue;
}

/* HIDE SPECIFIC FIELDS */
if(in_array($key, $hideFields)){
    continue;
}

/* HIDE ALL *_id FIELDS */
if(str_contains($key, "_id")){
    continue;
}

?>

<div class="detail-item">

    <b>

        <?php echo ucwords(str_replace("_"," ",$key)); ?> :

    </b>

    <?php echo nl2br(htmlspecialchars($value)); ?>

</div>

<?php
}
?>

    </div>

</div>

<!-- SERVICES -->

<div class="container">

    <div class="section-box"
         id="services">

        <h2 class="section-title">

            Our Services

        </h2>

        <div class="row g-4">

<?php

if(mysqli_num_rows($servicesResult) > 0){

while($service = mysqli_fetch_assoc($servicesResult)){

$image = !empty($service['picture'])
? "../uploads/services/" . $service['picture']
: "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1200";

?>

<div class="col-lg-4 col-md-6">

    <div class="service-card">

        <div class="wishlist-btn">

            <i class="fa fa-heart"></i>

        </div>

        <img src="<?php echo $image; ?>"
             class="service-image">

        <div class="service-body">

            <h4 class="service-title">

                <?php echo $service['service_name']; ?>

            </h4>

            <div class="service-price">

                ₹<?php echo number_format($service['price']); ?>

            </div>

            <p class="service-desc">

                <?php echo !empty($service['description'])
                ? $service['description']
                : "Professional event service available."; ?>

            </p>

           <div class="d-flex gap-2 mt-3">

    <!-- ADD TO CART -->
    <a href="add_to_cart.php?service_id=<?php echo $service['service_id']; ?>"
       class="btn btn-theme w-50">

        <i class="fa fa-cart-shopping me-2"></i>

        Add to Cart

    </a>

    <!-- BOOK NOW -->
    <a href="booking_form.php?service_id=<?php echo $service['service_id']; ?>&vendor_id=<?php echo $vendor_id; ?>"
       class="btn btn-dark w-50"
       style="
       border-radius:14px;
       padding:12px;
       font-weight:600;
       ">

        <i class="fa fa-bolt me-2"></i>

        Book Now

    </a>

</div>

        </div>

    </div>

</div>

<?php } } else { ?>

<p>No services available.</p>

<?php } ?>

        </div>

    </div>

</div>

<!-- PACKAGES -->

<div class="container">

    <div class="section-box"
         id="packages">

        <h2 class="section-title">

            Packages

        </h2>

        <div class="row g-4">

<?php

if(mysqli_num_rows($packagesResult) > 0){

while($package = mysqli_fetch_assoc($packagesResult)){

$image = !empty($package['package_picture'])
? "../uploads/packages/" . $package['package_picture']
: "https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=1200";

?>

<div class="col-lg-4 col-md-6">

    <div class="service-card">

        <div class="wishlist-btn">

            <i class="fa fa-heart"></i>

        </div>

        <img src="<?php echo $image; ?>"
             class="service-image">

        <div class="service-body">

            <h4 class="service-title">

                <?php echo $package['package_name']; ?>

            </h4>

            <div class="service-price">

                ₹<?php echo number_format($package['price']); ?>

            </div>

            <p class="service-desc">

                <?php echo !empty($package['description'])
                ? $package['description']
                : "Custom event package available."; ?>

            </p>

            <a href="add_to_cart.php?package_id=<?php echo $package['package_id']; ?>"
               class="btn btn-theme">

                <i class="fa fa-cart-shopping me-2"></i>

                Add To Cart

            </a>

        </div>

    </div>

</div>

<?php } } else { ?>

<p>No packages available.</p>

<?php } ?>

        </div>

    </div>

</div>

<!-- PORTFOLIO -->

<div class="container mb-5">

    <div class="section-box"
         id="portfolio">

        <h2 class="section-title">

            Portfolio Gallery

        </h2>

        <div class="row g-4 gallery">

<?php

mysqli_data_seek($portfolioResult,0);

if(mysqli_num_rows($portfolioResult) > 0){

while($img = mysqli_fetch_assoc($portfolioResult)){

$imagePath = "../uploads/portfolio/" . $img['image_path'];

?>

<div class="col-lg-4 col-md-6">

    <img src="<?php echo $imagePath; ?>">

</div>

<?php } } else { ?>

<p>No portfolio images uploaded.</p>

<?php } ?>

        </div>

    </div>

</div>

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