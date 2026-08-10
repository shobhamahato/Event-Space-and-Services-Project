<?php
session_start();

$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* ================= LOGIN CHECK ================= */

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ================= USER DATA ================= */

$userQuery = mysqli_query($conn,
"SELECT * FROM users WHERE id='$user_id'");

$userData = mysqli_fetch_assoc($userQuery);

$userName = $userData['name'];

/* ================= SESSION CART STORAGE ================= */

$_SESSION['cart'] = [];

/* ================= REMOVE ITEM ================= */

if(isset($_GET['remove'])){

    $cart_id = $_GET['remove'];

    mysqli_query($conn,"
        DELETE FROM cart
        WHERE cart_id='$cart_id'
        AND user_id='$user_id'
    ");

    header("Location: cart.php");
    exit();
}

/* ================= FETCH CART ================= */

$query = "

SELECT

    cart.cart_id,

    vendors.vendor_id,
    vendors.business_name,
    vendors.vendor_type,
    vendors.owner_name,
    vendors.phone,

    services.service_id,
    services.service_name,
    services.price,
    services.description,
    services.picture,
    services.food_type

FROM cart

JOIN vendors
ON cart.vendor_id = vendors.vendor_id

JOIN services
ON cart.service_id = services.service_id

WHERE cart.user_id='$user_id'

ORDER BY cart.cart_id DESC

";

$result = mysqli_query($conn,$query);

/* ================= TOTAL ================= */

$total_q = "

SELECT SUM(services.price) AS total

FROM cart

JOIN services
ON cart.service_id = services.service_id

WHERE cart.user_id='$user_id'

";

$total_r = mysqli_query($conn,$total_q);

$total = mysqli_fetch_assoc($total_r)['total'] ?? 0;

/* ================= COUNTS ================= */

$wishlistCount = 0;

$wishlistQuery = mysqli_query($conn,
"SELECT * FROM wishlist WHERE user_id='$user_id'");

if($wishlistQuery){
    $wishlistCount = mysqli_num_rows($wishlistQuery);
}

$cartCount = mysqli_num_rows($result);

/* RESET POINTER */

mysqli_data_seek($result,0);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Cart</title>

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

/* ================= BODY ================= */

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

.cart-header{
    background:
    linear-gradient(135deg,#ff8fab,#b185db);

    padding:35px;

    border-radius:30px;

    color:white;

    margin-top:30px;

    box-shadow:
    0 20px 50px rgba(181,126,220,0.2);
}

.cart-header h2{
    font-weight:700;
}

.cart-header p{
    opacity:0.9;
}

/* ================= SUMMARY ================= */

.summary-box{
    background:rgba(255,255,255,0.82);

    backdrop-filter:blur(16px);

    padding:25px;

    border-radius:25px;

    margin-top:25px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.06);
}

/* ================= CART CARD ================= */

.cart-card{
    background:rgba(255,255,255,0.92);

    border-radius:30px;

    overflow:hidden;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.08);

    transition:0.35s;

    height:100%;
}

.cart-card:hover{
    transform:translateY(-10px);

    box-shadow:
    0 25px 50px rgba(181,126,220,0.25);
}

.cart-image{
    height:240px;
    overflow:hidden;
}

.cart-image img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:0.5s;
}

.cart-card:hover .cart-image img{
    transform:scale(1.05);
}

.cart-body{
    padding:24px;
}

/* ================= BADGES ================= */

.vendor-badge{
    background:#b85fc6;
    color:white;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.food-badge{
    background:#ffe5ec;
    color:#ff4d6d;
    padding:7px 14px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    margin-left:10px;
}

/* ================= TEXT ================= */

.service-title{
    font-size:24px;
    font-weight:700;
    color:#2b2d42;
    margin-top:18px;
}

.info-text{
    color:#666;
    margin-bottom:10px;
}

.price{
    font-size:24px;
    font-weight:700;
    color:#b85fc6;
}

/* ================= BUTTONS ================= */

.btn-theme{
    background:
    linear-gradient(135deg,#ff8fab,#b185db);

    border:none;

    color:white;

    border-radius:14px;

    padding:12px;

    font-weight:600;

    text-decoration:none;

    transition:0.35s;
}

.btn-theme:hover{
    color:white;

    transform:translateY(-3px);

    box-shadow:
    0 12px 25px rgba(181,126,220,0.25);
}

.btn-remove{
    background:#ff4d6d;
    color:white;
    border-radius:14px;
    padding:12px;
    text-decoration:none;
    transition:0.3s;
}

.btn-remove:hover{
    color:white;
    transform:translateY(-3px);
}

/* ================= EMPTY ================= */

.empty-box{
    background:white;
    padding:60px;
    border-radius:30px;
    text-align:center;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.08);
}

.empty-box i{
    font-size:70px;
    color:#ff4d6d;
    margin-bottom:20px;
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

    .cart-image{
        height:180px;
    }

    .cart-header{
        padding:25px;
    }

    .service-title{
        font-size:20px;
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

<!-- ================= MAIN ================= -->

<div class="container py-4">

    <!-- HEADER -->

    <div class="cart-header d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div>

            <h2>

                <i class="fa fa-cart-shopping me-2"></i>

                My Cart

            </h2>

            <p class="mb-0">

                Manage your selected services for the perfect event

            </p>

        </div>

        <a href="vendors.php"
           class="btn btn-light rounded-pill px-4 py-2">

            <i class="fa fa-arrow-left me-2"></i>

            Continue Booking

        </a>

    </div>

    <!-- SUMMARY -->

    <div class="summary-box d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div>

            <h4 class="fw-bold">

                <?php echo $cartCount; ?> Services Added

            </h4>

            <p class="text-muted mb-0">

                Ready for booking

            </p>

        </div>

        <div class="text-end">

            <h2 class="price">

                ₹<?php echo number_format($total); ?>

            </h2>

            <!-- <a href="book_all_form.php?book_all=1&amount=<?php echo $total; ?>"
               class="btn btn-theme px-4 mt-2">

                <i class="fa fa-bolt me-2"></i>

                Book All

            </a> -->

        </div>

    </div>

    <!-- CART ITEMS -->

    <div class="row mt-4 g-4">

<?php if(mysqli_num_rows($result)>0){ ?>

<?php while($item=mysqli_fetch_assoc($result)){

$_SESSION['cart'][] = [

    'vendor_id' => $item['vendor_id'],

    'service_id' => $item['service_id']

];

$image = !empty($item['picture'])

? "../uploads/services/".$item['picture']

: "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800&auto=format&fit=crop";

?>

<div class="col-lg-4 col-md-6">

    <div class="cart-card">

        <!-- IMAGE -->

        <div class="cart-image">

            <img src="<?php echo $image; ?>">

        </div>

        <!-- BODY -->

        <div class="cart-body">

            <div class="d-flex align-items-center flex-wrap gap-2">

                <span class="vendor-badge">

                    <?php echo ucfirst(str_replace("_"," ",$item['vendor_type'])); ?>

                </span>

                <?php if(!empty($item['food_type'])){ ?>

                    <span class="food-badge">

                        <?php echo ucfirst($item['food_type']); ?>

                    </span>

                <?php } ?>

            </div>

            <h3 class="service-title">

                <?php echo $item['service_name']; ?>

            </h3>

            <p class="info-text">

                <i class="fa fa-store me-2"></i>

                <?php echo $item['business_name']; ?>

            </p>

            <p class="info-text">

                <i class="fa fa-phone me-2"></i>

                <?php echo $item['phone']; ?>

            </p>

            <h4 class="price mb-4">

                ₹<?php echo number_format($item['price']); ?>

            </h4>

            <div class="d-grid gap-2">

                <!-- BOOK -->

                <a class="btn btn-theme"

                href="booking_form.php?vendor_id=<?php echo $item['vendor_id']; ?>&service_id=<?php echo $item['service_id']; ?>&cart_id=<?php echo $item['cart_id']; ?>&amount=<?php echo $item['price']; ?>">

                    <i class="fa fa-calendar-check me-2"></i>

                    Book Now

                </a>

                <!-- REMOVE -->

                <a class="btn-remove text-center"

                href="cart.php?remove=<?php echo $item['cart_id']; ?>">

                    <i class="fa fa-trash me-2"></i>

                    Remove

                </a>

            </div>

        </div>

    </div>

</div>

<?php } ?>

<?php } else { ?>

<div class="col-12">

    <div class="empty-box">

        <i class="fa fa-cart-shopping"></i>

        <h3 class="fw-bold">

            Your Cart is Empty

        </h3>

        <p class="text-muted">

            Start exploring vendors and add services to your cart

        </p>

        <a href="vendors.php"
           class="btn btn-theme px-4 mt-3">

            Browse Vendors

        </a>

    </div>

</div>

<?php } ?>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>