<?php 
session_start();

$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* FETCH USER */
$userQuery = mysqli_query($conn,
"SELECT * FROM users WHERE id='$user_id'");

$userData = mysqli_fetch_assoc($userQuery);

$userName = $userData['name'];

/* FETCH WISHLIST */
$query = "
SELECT 
vendors.*,

MIN(vendor_portfolio.image_path) AS image_path,

AVG(bookings.rating) AS avg_rating,

COUNT(bookings.rating) AS total_reviews

FROM wishlist

JOIN vendors
ON wishlist.vendor_id = vendors.vendor_id

LEFT JOIN vendor_portfolio
ON vendors.vendor_id = vendor_portfolio.vendor_id

LEFT JOIN bookings
ON vendors.vendor_id = bookings.vendor_id

WHERE wishlist.user_id='$user_id'

GROUP BY vendors.vendor_id
";

$result = mysqli_query($conn,$query);

/* WISHLIST COUNT */
$wishlistCount = mysqli_num_rows($result);

/* CART COUNT */
$cartCount = 0;

$cartQuery = mysqli_query($conn,
"SELECT * FROM cart WHERE user_id='$user_id'");

if($cartQuery){
    $cartCount = mysqli_num_rows($cartQuery);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Wishlist</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
    background:
    linear-gradient(135deg, #ffcccc, #cdb4db);

    font-family:'Poppins',sans-serif;
    overflow-x:hidden;
    padding-bottom:120px;
    color:#2d2d2d;
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

/* ================= HEADER ================= */

.wishlist-header{

    background:
    linear-gradient(135deg,#ffdde1,#cdb4db);

    border-radius:35px;

    padding:45px;

    margin-top:30px;

    box-shadow:
    0 20px 50px rgba(181,126,220,0.18);

    position:relative;
    overflow:hidden;
}

.wishlist-header::before{

    content:"";

    position:absolute;

    width:300px;
    height:300px;

    background:rgba(255,255,255,0.15);

    border-radius:50%;

    top:-100px;
    right:-80px;
}

.wishlist-header h2{

    font-size:42px;
    font-weight:700;
    color:#2b2d42;
}

.wishlist-header p{

    color:#555;
    font-size:17px;
    margin-top:12px;
}

.header-btn{

    background:white;

    color:#444;

    padding:14px 30px;

    border-radius:14px;

    font-weight:600;

    text-decoration:none;

    transition:0.3s;
}

.header-btn:hover{

    transform:translateY(-3px);

    color:#444;
}

/* ================= CARD ================= */

.event-card{
    border:none;
    border-radius:30px;

    overflow:hidden;

    background:rgba(255,255,255,0.92);

    transition:0.4s;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.08);

    position:relative;
}

.event-card:hover{
    transform:
    translateY(-12px)
    scale(1.02);

    box-shadow:
    0 25px 50px rgba(181,126,220,0.25);
}

.event-img{
    height:240px;
    width:100%;
    object-fit:cover;
    transition:0.5s;
}

.event-card:hover .event-img{
    transform:scale(1.05);
}

.card-body{
    padding:24px;
}

.event-badge{
    position:absolute;
    top:18px;
    left:18px;
    background:#b85fc6;
    color:white;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    z-index:2;
}

/* ================= WISHLIST ================= */

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

    cursor:pointer;

    z-index:2;

    box-shadow:
    0 5px 15px rgba(0,0,0,0.15);
}

.wishlist-btn i{
    color:red;
}

.vendor-title{
    font-size:22px;
    font-weight:700;
    color:#2b2d42;
}

.vendor-info{
    color:#666;
    margin-bottom:8px;
}

.rating{
    color:gold;
    margin-bottom:18px;
}

/* ================= BUTTON ================= */

.btn-theme{
    background:
    linear-gradient(135deg,#ff8fab,#b185db);

    border:none;
    color:white;

    border-radius:14px;

    padding:13px;

    font-weight:600;

    transition:0.35s;
}

.btn-theme:hover{
    transform:translateY(-3px);

    box-shadow:
    0 12px 25px rgba(181,126,220,0.25);

    color:white;
}

/* ================= EMPTY ================= */

.empty-box{
    background:white;
    border-radius:30px;
    padding:60px;
    text-align:center;

    box-shadow:
    0 15px 35px rgba(0,0,0,0.08);
}

.empty-box i{
    font-size:70px;
    color:#ff4d6d;
    margin-bottom:20px;
}

.empty-box h3{
    font-weight:700;
    color:#2b2d42;
}

.empty-box p{
    color:#666;
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

.hero-content h1{
    font-size:36px;
}

.event-img{
    height:180px;
}

.wishlist-header{
    padding:28px;
}

.wishlist-header h2{
    font-size:30px;
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

            <i class="bi bi-heart-fill nav-icon"></i>

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
                       href="orders.php">

                        <i class="bi bi-box me-2"></i>

                        Bookings

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

<!-- ================= HEADER ================= -->

<div class="container">

    <div class="wishlist-header d-flex justify-content-between align-items-center flex-wrap gap-4">

        <div>

            <h2>

                <i class="fa fa-heart text-danger me-2"></i>

                My Wishlist

            </h2>

            <p>

                Save your favorite vendors for your dream event experience

            </p>

        </div>

        <a href="vendors.php"
           class="header-btn">

            <i class="fa fa-arrow-left me-2"></i>

            Continue Browsing

        </a>

    </div>

</div>

<!-- ================= VENDORS ================= -->

<div class="container mt-5">

    <div id="vendorContainer" class="row g-4">

<?php

if(mysqli_num_rows($result) > 0){

while($row = mysqli_fetch_assoc($result)){

$name = $row['business_name'];
$type = $row['vendor_type'];

if(!empty($row['image_path'])){

$image = "../uploads/portfolio/" . $row['image_path'];

}else{

$image = "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800&auto=format&fit=crop";
}

?>

<div class="col-lg-3 col-md-4 col-6 vendor-card">

    <div class="card event-card">

        <div class="event-badge">

            <?php echo ucfirst(str_replace("_"," ",$type)); ?>

        </div>

        <div class="wishlist-btn"
             onclick="removeWishlist(this, <?php echo $row['vendor_id']; ?>)">

            <i class="fa fa-heart"></i>

        </div>

        <img src="<?php echo $image; ?>"
             class="event-img"
             alt="<?php echo $name; ?>">

        <div class="card-body">

            <h5 class="vendor-title">

                <?php echo $name; ?>

            </h5>

            <div class="vendor-info">

                <i class="fa fa-user me-2"></i>

                <?php echo $row['owner_name']; ?>

            </div>

            <div class="vendor-info">

                <i class="fa fa-phone me-2"></i>

                <?php echo $row['phone']; ?>

            </div>

           <div class="rating">

<?php

$rating = round($row['avg_rating'],1);

if(!$rating){
    $rating = 0;
}

for($i=1; $i<=5; $i++){

    if($rating >= $i){

        echo '<i class="fa fa-star"></i>';

    }elseif($rating >= ($i-0.5)){

        echo '<i class="fa fa-star-half-alt"></i>';

    }else{

        echo '<i class="fa fa-star text-secondary"></i>';
    }
}
?>

<span class="text-muted ms-1">

    (<?php echo $rating; ?>)

</span>

</div>

            <a href="details.php?vendor_id=<?php echo $row['vendor_id']; ?>"
               class="btn btn-theme w-100">

                <i class="fa fa-eye me-2"></i>

                View Details

            </a>

        </div>

    </div>

</div>

<?php
}

}else{
?>

<div class="col-12">

    <div class="empty-box">

        <i class="fa fa-heart-crack"></i>

        <h3>No Wishlist Items</h3>

        <p>

            You haven't added any vendors to your wishlist yet.

        </p>

        <a href="vendors.php"
           class="btn btn-theme px-5 mt-3">

            Browse Vendors

        </a>

    </div>

</div>

<?php
}
?>

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

    <a href="dashboard.php#categories"
       class="bottom-link">

        <i class="fa fa-th-large"></i>
        <br>

        Categories

    </a>

    <a href="wishlist.php"
       class="bottom-link"
       style="color:#b85fc6;">

        <i class="bi bi-heart-fill"></i>
        <br>

        Wishlist

    </a>

    <a href="cart.php"
       class="bottom-link">

        <i class="bi bi-cart"></i>
        <br>

        Cart

    </a>

    <a href="orders.php"
       class="bottom-link">

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

<!-- ================= JS ================= -->

<script>

function removeWishlist(el, vendorId){

    fetch("add_wishlist.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"vendor_id=" + vendorId
    })

    .then(response => response.text())

    .then(data => {

        data = data.trim();

        if(data == "removed"){

            location.reload();
        }

    });

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>