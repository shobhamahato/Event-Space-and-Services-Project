<?php
session_start();

/* LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

/* DATABASE CONNECTION */
$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* USER DATA */
$user_id = $_SESSION['user_id'];

$userQuery = mysqli_query($conn,
"SELECT * FROM users WHERE id='$user_id'");

$userData = mysqli_fetch_assoc($userQuery);

$userName = $userData['name'];

/* CATEGORY FILTER */
$category = "All";

if(isset($_GET['category'])){
    $category = $_GET['category'];
}


// Notification


$user_id = $_SESSION['user_id'];

$countQuery = mysqli_query($conn,
"SELECT COUNT(*) as total 
FROM notifications
WHERE user_id='$user_id'
AND is_read=0");

$countData = mysqli_fetch_assoc($countQuery);

$notification_count = $countData['total'];

?>

<a href="notifications.php" class="position-relative">

    <i class="fa-solid fa-bell"></i>

    <?php if($notification_count > 0){ ?>

        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

            <?php echo $notification_count; ?>

        </span>

    <?php } ?>

</a>

<?php
/* FETCH VENDORS */
$sql = "SELECT 
vendors.vendor_id,
vendors.business_name,
vendors.owner_name,
vendors.phone,
vendors.vendor_type,
MIN(vendor_portfolio.image_path) AS image_path

FROM vendors

LEFT JOIN vendor_portfolio
ON vendors.vendor_id = vendor_portfolio.vendor_id

WHERE vendors.status='approved'";

if($category != "All"){
    $sql .= " AND vendors.vendor_type='$category'";
}

$sql .= " GROUP BY vendors.vendor_id";

$result = mysqli_query($conn,$sql);

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
// Popular vendors
$popularVendorsQuery = "

SELECT 
    v.vendor_id,
    v.business_name,
      v.owner_name,
    v.phone,
    v.vendor_type,

    ROUND(AVG(b.rating),1) AS avg_rating,
    COUNT(b.rating) AS total_reviews,

    MIN(vp.image_path) AS image_path

FROM vendors v

LEFT JOIN bookings b
ON v.vendor_id = b.vendor_id

LEFT JOIN vendor_portfolio vp
ON v.vendor_id = vp.vendor_id

WHERE b.rating IS NOT NULL
AND v.status='approved'

GROUP BY 
    v.vendor_id,
    v.business_name,
    v.vendor_type

ORDER BY avg_rating DESC, total_reviews DESC

LIMIT 4

";

/* EXECUTE QUERY */
$popularVendors = mysqli_query($conn, $popularVendorsQuery);

if(!$popularVendors){
    die(mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>User Dashboard</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

/* ================= BODY ================= */

body{
    background:
    linear-gradient(135deg, #ffcccc, #cdb4db);

    font-family:'Poppins',sans-serif;
    overflow-x:hidden;
    padding-bottom:120px;
    color:#2d2d2d;
    min-height:100vh;
}
.nav-link-icon{
    position:relative;
    text-decoration:none;
    color:black;
    font-size:22px;
}

.icon-badge{
    position:absolute;
    top:-8px;
    right:-10px;
    background:red;
    color:white;
    font-size:11px;
    min-width:18px;
    height:18px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
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
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:40px;

    background:
    linear-gradient(135deg,#ffdde1,#cdb4db);

    border-radius:35px;
    padding:50px;

    overflow:hidden;

    box-shadow:
    0 20px 50px rgba(181,126,220,0.18);

    margin-top:30px;
}

.hero-content{
    flex:1;
}

.hero-tag{
    background:white;
    color:#b85fc6;

    padding:8px 18px;
    border-radius:30px;

    font-size:14px;
    font-weight:600;

    display:inline-block;
    margin-bottom:20px;
}

.hero-content h1{
    font-size:54px;
    font-weight:700;
    line-height:1.2;
    color:#2b2d42;
}

.hero-content p{
    color:#555;
    font-size:18px;
    margin-top:18px;
    max-width:550px;
}

.hero-buttons{
    display:flex;
    gap:16px;
    margin-top:30px;
}

.hero-btn-primary{
    background:linear-gradient(135deg,#ff8fab,#b185db);
    color:white;
    border:none;
    padding:14px 30px;
    border-radius:14px;
    font-weight:600;
    text-decoration:none;
    transition:0.3s;
}

.hero-btn-primary:hover{
    transform:translateY(-3px);
    color:white;
}

.hero-btn-secondary{
    background:white;
    color:#444;
    padding:14px 30px;
    border-radius:14px;
    font-weight:600;
    text-decoration:none;
    transition:0.3s;
}

.hero-btn-secondary:hover{
    transform:translateY(-3px);
}

.hero-image{
    flex:1;
}

.hero-image img{
    width:100%;
    border-radius:28px;
    object-fit:cover;
    height:420px;
}

/* ================= SEARCH ================= */

.search-box{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(12px);

    padding:22px;
    border-radius:24px;
    margin-top:30px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.05);
}

.search-input{
    border:none;
    background:#f8f0ff;
    border-radius:15px;
    padding:14px;
    width:100%;
}

.search-input:focus{
    box-shadow:none;
    border:2px solid #c77dff;
}

.sort-select{
    border:none;
    background:#f8f0ff;
    border-radius:15px;
    padding:14px;
}

.sort-select:focus{
    box-shadow:none;
    border:2px solid #c77dff;
}

/* ================= CATEGORY ================= */

.section-title{
    font-size:34px;
    font-weight:700;
    color:#2b2d42;
}

.category-scroll{
    display:flex;
    gap:20px;
    overflow-x:auto;
    padding-bottom:10px;
}

.category-scroll::-webkit-scrollbar{
    display:none;
}

.category-card{
    text-align:center;
    min-width:120px;

    background:rgba(255,255,255,0.85);

    border-radius:24px;

    padding:18px;

    text-decoration:none;
    color:#333;

    transition:0.35s;

    box-shadow:
    0 10px 25px rgba(0,0,0,0.06);
}

.category-card:hover{
    transform:translateY(-10px);
    background:#f3e8ff;
}

.category-card img{
    width:80px;
    height:80px;
    border-radius:50%;
    object-fit:cover;
    margin-bottom:10px;
}

.category-card p{
    font-weight:600;
    margin:0;
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
    color:#777;
}

.wishlist-btn.active i{
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

<!-- ================= NAVBAR ================= -->

<nav class="navbar d-flex justify-content-between align-items-center">

    <a class="navbar-brand" href="dashboard.php">

        <i class="fa fa-calendar-check me-2 logo-icon"></i>

        EventSpace

    </a>

    <div class="d-flex align-items-center gap-4">
        <!-- notification  -->
         <a href="notifications.php" 
            class="nav-link-icon">

                <i class="bi bi-bell nav-icon"></i>

                <?php if($notificationCount > 0){ ?>

                <span class="icon-badge">
                    <?php echo $notificationCount; ?>
                </span>

                <?php } ?>

            </a>
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

<!-- ================= HERO ================= -->

<div class="container">

    <div class="hero-section">

        <div class="hero-content">

            <!-- <span class="hero-tag">
                ✨ Premium Event Planning Platform
            </span> -->

            <h1>
                Plan Your Dream Event With Ease
            </h1>

            <p>
                Discover decorators, photographers, caterers,
                venues and top-rated event vendors.
            </p>

            <div class="hero-buttons">

                <a href="vendors.php"
                   class="hero-btn-primary">

                    Explore Vendors

                </a>

                <a href="#categories"
                   class="hero-btn-secondary">

                    Browse Categories

                </a>

            </div>

        </div>

        <div class="hero-image">

            <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=1200&auto=format&fit=crop">

        </div>

    </div>

</div>

<!-- ================= SEARCH ================= -->

<div class="container">

    <div class="search-box">

        <div class="row align-items-center g-3">

            <div class="col-md-8">

                <input type="text"
                       class="form-control search-input"
                       id="searchInput"
                       placeholder="Search vendors...">

            </div>

            <div class="col-md-4">

                <select class="form-select sort-select">

                    <option>Sort By</option>
                    <option>Top Rated</option>
                    <option>Popularity</option>
                    <option>Newest</option>

                </select>

            </div>

        </div>

    </div>

</div>

<!-- ================= CATEGORY ================= -->

<div class="container mt-5" id="categories">

    <h3 class="section-title mb-4">
        Browse Categories
    </h3>

    <div class="category-scroll">
        <a href="vendors.php"
           class="category-card">

            <img src="../vendors_images/vendor_cart.png">

            <p>All </p>

        </a>

        <a href="vendors.php?category=decorator"
           class="category-card">

            <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=300&auto=format&fit=crop">

            <p>Decor</p>

        </a>

        <a href="vendors.php?category=photography"
           class="category-card">

            <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=300&auto=format&fit=crop">

            <p>Photography</p>

        </a>

        <a href="vendors.php?category=caterer"
           class="category-card">

            <img src="https://images.unsplash.com/photo-1555244162-803834f70033?q=80&w=300&auto=format&fit=crop">

            <p>Catering</p>

        </a>

        <a href="vendors.php?category=music_dj"
           class="category-card">

            <img src="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?q=80&w=300&auto=format&fit=crop">

            <p>Music</p>

        </a>

        <a href="vendors.php?category=venue"
           class="category-card">

            <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=300&auto=format&fit=crop">

            <p>Venues</p>

        </a>

        <a href="vendors.php?category=beauty_parlour"
           class="category-card">

            <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?q=80&w=300&auto=format&fit=crop">

            <p>Beauty</p>

        </a>

        <a href="vendors.php?category=card_vendor"
           class="category-card">

            <img src="https://images.unsplash.com/photo-1517842645767-c639042777db?q=80&w=300&auto=format&fit=crop">

            <p>Cards</p>

        </a>

    </div>

</div>

<!-- ================= VENDORS ================= -->

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3 class="section-title">
            Popular Vendors
        </h3>

    </div>

    <div id="vendorContainer" class="row g-4">

<?php
if($popularVendors && mysqli_num_rows($popularVendors) > 0){

while($row = mysqli_fetch_assoc($popularVendors)){

    $vendorId = $row['vendor_id'];

    $checkWishlist = mysqli_query($conn,
    "SELECT * FROM wishlist
    WHERE user_id='$user_id'
    AND vendor_id='$vendorId'");

    $isWishlist = mysqli_num_rows($checkWishlist) > 0;

    if(!empty($row['image_path'])){

        $image = "../uploads/portfolio/" . $row['image_path'];

    }else{

        $image = "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800&auto=format&fit=crop";
    }

?>

<div class="col-lg-3 col-md-4 col-6 vendor-card"
     data-name="<?php echo strtolower($row['business_name']); ?>">

    <div class="card event-card">

        <div class="event-badge">

            <?php echo ucfirst(str_replace("_"," ",$row['vendor_type'])); ?>

        </div>

        <!-- Wishlist -->
        <div class="wishlist-btn <?php if($isWishlist){ echo 'active'; } ?>"
             onclick="toggleWishlist(this, <?php echo $vendorId; ?>)">

            <i class="fa fa-heart"></i>

        </div>

        <!-- Image -->
        <img src="<?php echo $image; ?>"
             class="event-img">

        <!-- Body -->
        <div class="card-body">

            <h5 class="vendor-title">

                <?php echo $row['business_name']; ?>

            </h5>

            <div class="vendor-info">

                <i class="fa fa-user me-1"></i>

                <?php echo $row['owner_name']; ?>

            </div>

            <div class="vendor-info">

                <i class="fa fa-phone me-1"></i>

                <?php echo $row['phone']; ?>

            </div>

            <div class="rating">

    <i class="fa fa-star"></i>

    <span class="text-muted ms-1">

        <?php echo $row['avg_rating']; ?>

        (<?php echo $row['total_reviews']; ?> Reviews)

    </span>

</div>

            <a href="details.php?vendor_id=<?php echo $vendorId; ?>"
               class="btn btn-theme w-100">

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

<!-- ================= BOTTOM NAV ================= -->

<div class="bottom-nav">

    <a href="dashboard.php"
       class="bottom-link">

        <i class="fa fa-home"></i>
        <br>

        Home

    </a>

    <a href="#categories"
       class="bottom-link">

        <i class="fa fa-th-large"></i>
        <br>

        Categories

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

<!-- ================= SCRIPT ================= -->

<script>

/* SEARCH */

document.getElementById("searchInput")
.addEventListener("keyup", function(){

    let value = this.value.toLowerCase();

    let cards = document.querySelectorAll(".vendor-card");

    cards.forEach(card => {

        let name = card.dataset.name;

        if(name.includes(value)){

            card.style.display = "block";

        }else{

            card.style.display = "none";
        }

    });

});

/* WISHLIST */

function toggleWishlist(el,vendorId){

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

        }else if(data == "removed"){

            el.classList.remove("active");
        }

    });

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>