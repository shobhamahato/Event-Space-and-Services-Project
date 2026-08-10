<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
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
?>

<!-- ================= NAVBAR + BOTTOM NAV CSS ================= -->

<style>

/* ================= TOP NAVBAR ================= */

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

</style>

<!-- ================= TOP NAVBAR ================= -->

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

<!-- ================= BOTTOM NAVBAR ================= -->

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

    <a href="profile.php"
       class="bottom-link">

        <i class="bi bi-person"></i>
        <br>

        Profile

    </a>

</div>