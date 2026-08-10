<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/db.php');

$user_id = $_SESSION['user_id'];

/* ================= USER DATA ================= */

$userQuery = mysqli_query($conn,
"SELECT * FROM users WHERE id='$user_id'");

$userData = mysqli_fetch_assoc($userQuery);

$userName = $userData['name'];

/* ================= FETCH BOOKINGS HISTORY ================= */

$query = mysqli_query($conn,

"SELECT 

bookings.*,

vendors.business_name,

services.service_name,
services.picture,
services.description

FROM bookings

LEFT JOIN vendors
ON bookings.vendor_id = vendors.vendor_id

LEFT JOIN services
ON bookings.service_id = services.service_id

WHERE bookings.user_id='$user_id'

AND bookings.booking_status='Confirmed'

AND bookings.payment_status='Paid'

ORDER BY bookings.id DESC");

if(!$query){
    die("Query Failed : " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Booking History</title>

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
    min-height:100vh;
    padding-bottom:120px;
}

/* ================= NAVBAR ================= */

.navbar{
    background:rgba(255,255,255,0.78);
    backdrop-filter:blur(18px);
    padding:14px 35px;
    position:sticky;
    top:0;
    z-index:999;
    box-shadow:0 4px 20px rgba(181,126,220,0.12);
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

.profile{
    display:flex;
    align-items:center;
    gap:12px;
    background:white;
    padding:7px 14px;
    border-radius:50px;
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

/* ================= TITLE ================= */

.page-title-box{
    background:rgba(255,255,255,0.82);
    backdrop-filter:blur(12px);
    border-radius:30px;
    padding:30px;
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
    margin-top:6px;
}

/* ================= CARD ================= */

.history-card{
    background:rgba(255,255,255,0.9);
    backdrop-filter:blur(12px);
    border-radius:28px;
    overflow:hidden;
    margin-bottom:25px;
    box-shadow:0 12px 35px rgba(0,0,0,0.08);
    transition:0.35s;
}

.history-card:hover{
    transform:translateY(-5px);
    box-shadow:0 20px 45px rgba(181,126,220,0.18);
}

.history-wrapper{
    display:flex;
    align-items:center;
    gap:22px;
    padding:22px;
}

.history-image{
    width:260px;
    height:190px;
    object-fit:cover;
    border-radius:24px;
    flex-shrink:0;
}

.history-content{
    flex:1;
}

.vendor-name{
    font-size:28px;
    font-weight:700;
    color:#2b2d42;
    margin-bottom:5px;
}

.service-name{
    color:#7c3aed;
    font-weight:600;
    margin-bottom:14px;
    font-size:15px;
}

.service-description{
    color:#666;
    font-size:14px;
    line-height:1.7;
    margin-bottom:18px;
}

.info-box{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    margin-bottom:18px;
}

.info-pill{
    background:#f5ecff;
    color:#5a189a;
    padding:10px 16px;
    border-radius:50px;
    font-size:13px;
    font-weight:600;
}

.status-box{
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    margin-top:15px;
}

.badge-confirmed{
    background:#dcfce7;
    color:#15803d;
    padding:10px 18px;
    border-radius:50px;
    font-size:13px;
    font-weight:700;
}

.badge-paid{
    background:#dbeafe;
    color:#1d4ed8;
    padding:10px 18px;
    border-radius:50px;
    font-size:13px;
    font-weight:700;
}

.price{
    font-size:32px;
    font-weight:700;
    color:#16a34a;
    margin-top:18px;
}

/* ================= SUCCESS BOX ================= */

.success-box{
    width:320px;
    background:#faf5ff;
    border-radius:24px;
    padding:24px;
    text-align:center;
    flex-shrink:0;
}

.success-icon{
    width:80px;
    height:80px;
    border-radius:50%;
    background:#dcfce7;
    color:#16a34a;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    font-size:34px;
}

.success-title{
    font-size:20px;
    font-weight:700;
    color:#15803d;
    margin-top:18px;
}

.success-text{
    color:#666;
    margin-top:8px;
    font-size:14px;
}

/* ================= EMPTY ================= */

.empty-box{
    background:rgba(255,255,255,0.9);
    border-radius:30px;
    padding:70px 20px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
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

/* ================= BOTTOM NAV ================= */

.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    width:100%;
    background:rgba(255,255,255,0.82);
    backdrop-filter:blur(18px);
    padding:16px 0;
    display:flex;
    justify-content:space-around;
    box-shadow:0 -5px 25px rgba(181,126,220,0.15);
    z-index:999;
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

.history-wrapper{
    flex-direction:column;
}

.history-image{
    width:100%;
    height:240px;
}

.success-box{
    width:100%;
}

}

@media(max-width:768px){

.page-title{
    font-size:28px;
}

.vendor-name{
    font-size:23px;
}

.price{
    font-size:25px;
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

    <div class="profile">

        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=ff4f9a&color=fff">

        <span>
            <?php echo htmlspecialchars($userName); ?>
        </span>

    </div>

</nav>

<!-- ================= TITLE ================= -->

<div class="container">

    <div class="page-title-box">

        <h1 class="page-title">
            Booking History
        </h1>

        <p class="page-subtitle">
            Your completed and successfully paid bookings.
        </p>

    </div>

</div>

<!-- ================= HISTORY ================= -->

<div class="container">

<?php if(mysqli_num_rows($query) > 0){ ?>

<?php while($row = mysqli_fetch_assoc($query)){ ?>

<div class="history-card">

    <div class="history-wrapper">

        <!-- IMAGE -->

        <?php

        if(!empty($row['picture'])){

            $image = "../uploads/services/" . $row['picture'];

        }else{

            $image = "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1200&auto=format&fit=crop";
        }

        ?>

        <img src="<?php echo $image; ?>"
             class="history-image">

        <!-- CONTENT -->

        <div class="history-content">

            <h2 class="vendor-name">

                <?php echo htmlspecialchars($row['business_name']); ?>

            </h2>

            <div class="service-name">

                <?php echo htmlspecialchars($row['service_name']); ?>

            </div>

            <?php if(!empty($row['description'])){ ?>

            <div class="service-description">

                <?php echo htmlspecialchars(substr($row['description'],0,150)); ?>...

            </div>

            <?php } ?>

            <!-- INFO -->

            <div class="info-box">

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

            <!-- STATUS -->

            <div class="status-box">

                <span class="badge-confirmed">

                    <i class="fa fa-circle-check me-2"></i>

                    <?php echo htmlspecialchars($row['booking_status']); ?>

                </span>

                <span class="badge-paid">

                    <i class="fa fa-wallet me-2"></i>

                    <?php echo htmlspecialchars($row['payment_status']); ?>

                </span>

            </div>

            <!-- PRICE -->

            <div class="price">

                ₹<?php echo number_format($row['amount'],2); ?>

            </div>

        </div>

        <!-- SUCCESS BOX -->

        <div class="success-box">

            <div class="success-icon">

                <i class="fa-solid fa-check"></i>

            </div>

            <div class="success-title">

                Booking Completed

            </div>

            <div class="success-text">

                Your booking has been successfully confirmed and payment has been received.

            </div>

        </div>

    </div>

</div>

<?php } ?>

<?php } else { ?>

<div class="empty-box">

    <i class="fa-regular fa-calendar-xmark"></i>

    <h3>No Booking History Found</h3>

    <p>
        Your confirmed and paid bookings will appear here.
    </p>

    <a href="dashboard.php"
       class="btn btn-lg mt-3 text-white"
       style="background:linear-gradient(135deg,#ff8fab,#b185db); border:none; border-radius:14px; padding:12px 28px;">

        Explore Vendors

    </a>

</div>

<?php } ?>

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

    <a href="bookings_history.php"
       class="bottom-link"
       style="color:#b85fc6;">

        <i class="bi bi-clock-history"></i>
        <br>

        History

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