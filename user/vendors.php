<?php
session_start();

/* DATABASE CONNECTION */
$conn = new mysqli("localhost", "root", "", "event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* CATEGORY FILTER */
$category = "All";

if(isset($_GET['category'])){
    $category = $_GET['category'];
}

/* FETCH APPROVED VENDORS */
$sql = "SELECT 

vendors.vendor_id,
vendors.business_name,
vendors.owner_name,
vendors.phone,
vendors.vendor_type,

MIN(vendor_portfolio.image_path) AS image_path,

AVG(bookings.rating) AS avg_rating,
COUNT(bookings.rating) AS total_reviews

FROM vendors

LEFT JOIN vendor_portfolio
ON vendors.vendor_id = vendor_portfolio.vendor_id

LEFT JOIN bookings
ON vendors.vendor_id = bookings.vendor_id

WHERE vendors.status='approved'";

/* CATEGORY FILTER */
if($category != "All"){
    $sql .= " AND vendors.vendor_type='$category'";
}

/* GROUP BY */
$sql .= " GROUP BY vendors.vendor_id";

/* EXECUTE QUERY */
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Vendors</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
    background:linear-gradient(135deg, #ffcccc, #cdb4db);
    font-family:'Poppins',sans-serif;
    overflow-x:hidden;
    padding-bottom:100px;
    color:#2d2d2d;
    min-height:100vh;
}

/* ================= NAVBAR ================= */

.navbar{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(18px);
    padding:12px 35px;
    width:100%;
    box-shadow:0 4px 20px rgba(181,126,220,0.15);
    position:sticky;
    top:0;
    z-index:999;
    border-radius:0 0 24px 24px;
}

.navbar-brand{
    color:#b85fc6;
    font-size:28px;
    font-weight:700;
    text-decoration:none;
}

.navbar-brand:hover{
    color:#b85fc6;
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

/* ================= TOP BAR ================= */

.top-bar{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(18px);
    padding:28px;
    border-radius:28px;
    box-shadow:0 10px 30px rgba(181,126,220,0.12);
}

.vendor-title{
    font-weight:700;
    font-size:34px;
    color:#2b2d42;
}

/* ================= SEARCH ================= */

.search-box{
    border:none;
    background:rgba(255,255,255,0.8);
    backdrop-filter:blur(12px);
    border-radius:18px;
    padding:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.search-box:focus{
    box-shadow:none;
    border:2px solid #c77dff;
    background:white;
}

/* ================= CARD ================= */

.event-card{
    border:none;
    border-radius:30px;
    overflow:hidden;
    background:rgba(255,255,255,0.92);
    transition:0.4s;
    box-shadow:0 15px 40px rgba(0,0,0,0.08);
    position:relative;
    height:100%;
}

.event-card:hover{
    transform:translateY(-12px) scale(1.02);
    box-shadow:0 25px 50px rgba(181,126,220,0.25);
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

/* ================= BADGE ================= */

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
    transition:0.3s;
    box-shadow:0 5px 15px rgba(0,0,0,0.15);
}

.wishlist-btn i{
    color:#777;
    font-size:18px;
}

.wishlist-btn.active i{
    color:red;
}

.wishlist-btn:hover{
    transform:scale(1.1);
}

/* ================= CARD BODY ================= */

.card-body{
    padding:24px;
}

.card-body h5{
    font-weight:700;
    color:#2b2d42;
    font-size:22px;
}

.text-muted{
    color:#666 !important;
}

.price{
    color:#b85fc6;
    font-weight:700;
}

/* ================= RATING ================= */

.rating i{
    color:gold;
    font-size:15px;
}

/* ================= BUTTON ================= */

.btn-theme{
    background:linear-gradient(135deg,#ff8fab,#b185db);
    border:none;
    color:white;
    border-radius:14px;
    padding:13px;
    font-weight:600;
    transition:0.35s;
}

.btn-theme:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 25px rgba(181,126,220,0.25);
    color:white;
}

/* ================= DARK BUTTON ================= */

.btn-dark{
    background:linear-gradient(135deg,#ff8fab,#b185db);
    border:none;
    border-radius:14px;
    padding:12px 22px;
    font-weight:600;
}

.btn-dark:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(181,126,220,0.25);
}

/* ================= BOTTOM NAV ================= */

.bottom-link{
    text-decoration:none;
    color:#555;
    transition:0.3s;
}

.bottom-link:hover{
    color:#b85fc6;
}

/* ================= MOBILE ================= */


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

.hero-section{
    flex-direction:column;
    padding:30px;
}

.hero-content h1{
    font-size:36px;
}

.hero-image img{
    height:260px;
}

.event-img{
    height:180px;
}

}


</style>

</head>

<body>
<?php

/* CHECK LOGIN */
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

/* FETCH USER DATA */
$user_id = $_SESSION['user_id'];

$userQuery = "SELECT * FROM users WHERE id='$user_id'";

$userResult = mysqli_query($conn, $userQuery);

$userData = mysqli_fetch_assoc($userResult);

/* USER NAME */
$userName = $userData['name'];
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
?>

<!-- NAVBAR -->
<nav class="navbar d-flex justify-content-between align-items-center px-4">

    <!-- LOGO -->
    <a class="navbar-brand" href="dashboard.php">

        <i class="fa fa-calendar-check me-2"
           style="color:#1cc88a;"></i>

        EventSpace

    </a>

    <!-- RIGHT SIDE -->
    <div class="nav-icons d-flex align-items-center gap-4">

        <!-- WISHLIST -->
            <a href="wishlist.php"
        class="nav-link-icon position-relative wishlist-nav">

            <i class="bi bi-heart nav-icon"></i>

            <?php if($wishlistCount > 0){ ?>

                <span class="icon-badge wishlist-count">
                    <?php echo $wishlistCount; ?>
                </span>

            <?php } ?>

        </a>

        <!-- CART -->
        <a href="cart.php"
           class="nav-link-icon position-relative">

            <i class="bi bi-cart nav-icon"></i>

            <?php if($cartCount > 0){ ?>

                <span class="icon-badge">
                    <?php echo $cartCount; ?>
                </span>

            <?php } ?>

        </a>

        <!-- PROFILE -->
        <div class="dropdown">

            <div class="profile dropdown-toggle"
                 data-bs-toggle="dropdown"
                 aria-expanded="false">

                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=1cc88a&color=fff">

                <span>
                    <?php echo htmlspecialchars($userName); ?>
                </span>

            </div>

            <!-- DROPDOWN -->
            <ul class="dropdown-menu dropdown-menu-end profile-menu">

                <li class="dropdown-header">

                    <?php echo htmlspecialchars($userName); ?>

                </li>

                <li>

                    <a class="dropdown-item"
                       href="profile.php">

                        <i class="bi bi-person-circle me-2"
                           style="color:black;"></i>

                        Profile

                    </a>

                </li>

                <li>

                    <a class="dropdown-item"
                       href="dashboard.php">

                        <i class="bi bi-heart me-2"
                           style="color:black;"></i>

                        Dasboard

                    </a>

                </li>

                <li><hr class="dropdown-divider"></li>

                <li>

                    <a class="dropdown-item text-danger"
                       href="../auth/logout.php">

                        <i class="bi bi-box-arrow-right me-2"
                           style="color:black;"></i>

                        Logout

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>
<div class="container py-5">

    <!-- HEADER -->
    <div class="top-bar mb-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <!-- TITLE -->
            <div>

                <h2 class="vendor-title mb-1">

                <?php

                if($category == "All"){

                    echo "All Vendors";

                }else{

                    echo ucfirst(str_replace("_"," ",$category)) . " Vendors";
                }

                ?>

                </h2>

                <p class="text-muted mb-0">
                    Explore the best verified vendors for your events
                </p>

            </div>

            <!-- BACK BUTTON -->
            <a href="dashboard.php" class="btn btn-dark">

                <i class="fa fa-arrow-left me-1"></i>
                Back

            </a>

        </div>

    </div>

    <!-- SEARCH -->
    <div class="mb-4">

        <input type="text"
               id="searchInput"
               class="form-control search-box"
               placeholder="Search vendors...">

    </div>

    <!-- VENDOR CARDS -->
    <div id="vendorContainer" class="row g-4">

<?php

if(!$result || mysqli_num_rows($result)==0){

    echo "<h5 class='text-center text-muted'>No Vendors Found</h5>";

}else{

    while($row = mysqli_fetch_assoc($result)){

        $name = $row['business_name'];
        $type = $row['vendor_type'];

        /* IMAGE */
        if(!empty($row['image_path'])){

            $image = "../uploads/portfolio/" . $row['image_path'];

        }else{

            $image = "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800&auto=format&fit=crop";
        }

?>

<div class="col-lg-3 col-md-4 col-6 vendor-card"
     data-name="<?php echo strtolower($name); ?>"
     data-type="<?php echo strtolower($type); ?>">

    <div class="card event-card">

        <!-- BADGE -->
        <div class="event-badge">
            <?php echo ucfirst(str_replace("_"," ",$type)); ?>
        </div>

        <!-- IMAGE -->
        <img src="<?php echo $image; ?>"
             class="event-img"
             alt="<?php echo $name; ?>">

        <!-- WISHLIST -->
                <?php

        $vendorId = $row['vendor_id'];

        $checkWishlist = mysqli_query($conn,
        "SELECT * FROM wishlist
        WHERE user_id='$user_id'
        AND vendor_id='$vendorId'");

        $isWishlist = mysqli_num_rows($checkWishlist) > 0;

        ?>

        <!-- WISHLIST -->
        <div class="wishlist-btn <?php if($isWishlist){ echo 'active'; } ?>"
            onclick="toggleWishlist(this, <?php echo $vendorId; ?>)">

            <i class="fa fa-heart"></i>

        </div>

        <!-- BODY -->
        <div class="card-body">

            <h5>
                <?php echo $name; ?>
            </h5>

            <p class="text-muted mb-1">
                <i class="fa fa-user me-1"></i>
                <?php echo $row['owner_name']; ?>
            </p>

            <p class="text-muted mb-2">
                <i class="fa fa-phone me-1"></i>
                <?php echo $row['phone']; ?>
            </p>

            <!-- RATING -->
            <!-- RATING -->

<?php

$avg_rating = round($row['avg_rating'],1);

$total_reviews = $row['total_reviews'];

if(!$avg_rating){
    $avg_rating = 0;
}

?>

<div class="rating mb-2">

<?php

$fullStars = floor($avg_rating);

$halfStar = ($avg_rating - $fullStars >= 0.5);

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
<p class="text-muted small">
<?php echo $total_reviews; ?> Reviews
</p>
            <p class="price">
                Verified Vendor
            </p>

         <a href="details.php?vendor_id=<?php echo $row['vendor_id']; ?>" 
                class="btn btn-theme w-100">

                    <i class="fa fa-eye me-1"></i>
                    View Details

                </a>

        </div>

    </div>

</div>

<?php
    }
}
?>

    </div>

</div>
<!-- !-- ================= BOTTOM NAV ================= -->

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
       class="bottom-link"
       style="color:#b85fc6;">

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
<!-- SEARCH JS -->
<script>

document.getElementById("searchInput").addEventListener("keyup", function(){

    let value = this.value.toLowerCase();

    let cards = document.querySelectorAll(".vendor-card");

    cards.forEach(card => {

        let name = card.dataset.name;
        let type = card.dataset.type;

        if(name.includes(value) || type.includes(value)){

            card.style.display = "block";

        }else{

            card.style.display = "none";
        }

    });

});

/* WISHLIST */
function toggleWishlist(el, vendorId){

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

        if(data == "added"){

            el.classList.add("active");

            location.reload();
        }

        else if(data == "removed"){

            el.classList.remove("active");

            location.reload();
        }

        else if(data == "login_required"){

            alert("Please login first");
        }

    });

}
</script>

</body>
</html>