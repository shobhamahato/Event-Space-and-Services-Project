<?php 
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/db.php');

$uid = $_SESSION['user_id'];

$result = mysqli_query($conn,"SELECT * FROM users WHERE id='$uid'");
$user = mysqli_fetch_assoc($result);

$userName = $user['name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Profile</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
    background:linear-gradient(135deg,#ffcccc,#cdb4db);
    font-family:'Poppins',sans-serif;
    overflow-x:hidden;
    min-height:100vh;
    color:#2d2d2d;
    padding-bottom:50px;
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
}

.navbar-brand{
    color:#b85fc6 !important;
    font-size:28px;
    font-weight:700;
    text-decoration:none;
}

.logo-icon{
    color:#ff8fab;
}

/* ICON BOX */

.nav-icon-box{
    width:44px;
    height:44px;
    border-radius:50%;
    background:rgba(255,255,255,0.85);
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:0.3s;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

.nav-icon-box:hover{
    transform:translateY(-3px);
    background:#f3e8ff;
}

.nav-icon{
    color:#555;
    font-size:18px;
    transition:0.3s;
}

.nav-icon-box:hover .nav-icon{
    color:#b85fc6;
}

/* PROFILE */

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
    color:#444 !important;
    font-weight:600;
}

/* DROPDOWN */

.profile-menu{
    border:none;
    border-radius:20px;
    padding:10px;
    min-width:230px;
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

/* ================= PROFILE SECTION ================= */

.profile-container{
    width:95%;
    max-width:1200px;
    margin:auto;
    padding:40px 0;
}

.profile-card{
    background:rgba(255,255,255,0.78);
    backdrop-filter:blur(18px);
    border-radius:35px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(181,126,220,0.18);
}

/* COVER */

.cover-section{
    background:linear-gradient(135deg,#ff8fab,#b185db);
    height:220px;
    position:relative;
}

/* PROFILE IMAGE */

.profile-image{
    position:absolute;
    bottom:-60px;
    left:50%;
    transform:translateX(-50%);
}

.profile-image img{
    width:135px;
    height:135px;
    border-radius:50%;
    border:6px solid white;
    object-fit:cover;
    box-shadow:0 12px 30px rgba(0,0,0,0.18);
}

/* CONTENT */

.profile-content{
    padding:90px 40px 40px;
}

.user-name{
    text-align:center;
    font-weight:700;
    font-size:34px;
    color:#2b2d42;
}

.user-role{
    text-align:center;
    color:#666;
    margin-bottom:40px;
    font-size:16px;
}

/* INFO BOX */

.info-box{
    background:rgba(255,255,255,0.92);
    border-radius:24px;
    padding:24px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    height:100%;
    transition:0.35s;
}

.info-box:hover{
    transform:translateY(-8px);
    box-shadow:0 20px 40px rgba(181,126,220,0.18);
}

/* ICON */

.info-icon{
    width:60px;
    height:60px;
    border-radius:18px;
    background:#f3e8ff;
    color:#b85fc6;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    margin-bottom:18px;
}

/* TEXT */

.info-title{
    font-size:14px;
    color:#777;
    margin-bottom:6px;
}

.info-value{
    font-size:18px;
    font-weight:600;
    color:#2b2d42;
    word-break:break-word;
}

/* BUTTON */

.btn-edit{
    background:linear-gradient(135deg,#ff8fab,#b185db);
    border:none;
    color:white;
    padding:14px 34px;
    border-radius:16px;
    font-weight:600;
    transition:0.35s;
    text-decoration:none;
    display:inline-block;
}

.btn-edit:hover{
    transform:translateY(-3px);
    color:white;
    box-shadow:0 12px 25px rgba(181,126,220,0.25);
}

/* MOBILE */

@media(max-width:768px){

    .navbar{
        padding:12px 18px;
    }

    .navbar-brand{
        font-size:22px;
    }

    .profile span{
        display:none;
    }

    .profile-content{
        padding:85px 20px 30px;
    }

    .user-name{
        font-size:26px;
    }

    .cover-section{
        height:180px;
    }

    .profile-image img{
        width:115px;
        height:115px;
    }

    .info-box{
        padding:20px;
    }

}
</style>

</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg">

    <div class="container-fluid">

        <!-- LOGO -->
        <a class="navbar-brand" href="dashboard.php">

            <i class="fa fa-calendar-check me-2 logo-icon"></i>

            EventSpace

        </a>

        <!-- MOBILE BUTTON -->
        <button class="navbar-toggler border-0 shadow-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarContent">

            <i class="fa fa-bars text-white"></i>

        </button>

        <!-- NAVBAR CONTENT -->
        <div class="collapse navbar-collapse justify-content-end"
             id="navbarContent">

            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                <!-- PROFILE -->
                <div class="dropdown">

                    <div class="profile dropdown-toggle"
                         data-bs-toggle="dropdown">

                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=1cc88a&color=fff">

                        <span class="text-white fw-semibold">
                            <?php echo htmlspecialchars($userName); ?>
                        </span>

                    </div>

                    <ul class="dropdown-menu dropdown-menu-end profile-menu">

                        <li>
                            <a class="dropdown-item"
                               href="dashboard.php">

                               <i class="bi bi-house-door me-2"></i>

                               Dashboard

                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="profile.php">

                               <i class="bi bi-person-circle me-2"></i>

                               My Profile

                            </a>
                        </li>

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

        </div>

    </div>

</nav>

<!-- ================= PROFILE SECTION ================= -->

<div class="profile-container">

    <div class="profile-card">

        <!-- COVER -->
        <div class="cover-section">

            <div class="profile-image">

                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=1cc88a&color=fff&size=200">

            </div>

        </div>

        <!-- CONTENT -->
        <div class="profile-content">

            <h2 class="user-name">
                <?php echo htmlspecialchars($user['name']); ?>
            </h2>

            <p class="user-role">
                EventSpace User
            </p>

            <!-- INFO -->
            <div class="row g-4">
                    <!-- YOUR BOOKINGS -->

<div class="col-md-6">

    <a href="bookings_history.php"
       style="text-decoration:none;">

        <div class="info-box">

            <div class="info-icon">

                <i class="bi bi-clock-history"></i>

            </div>

            <div class="info-title">
                Your Bookings
            </div>

            <div class="info-value">
                View Booking History
            </div>

        </div>

    </a>

</div>
                <!-- EMAIL -->
                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-icon">

                            <i class="bi bi-envelope"></i>

                        </div>

                        <div class="info-title">
                            Email Address
                        </div>

                        <div class="info-value">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>

                    </div>

                </div>

                <!-- MOBILE -->
                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-icon">

                            <i class="bi bi-phone"></i>

                        </div>

                        <div class="info-title">
                            Mobile Number
                        </div>

                        <div class="info-value">
                            <?php echo htmlspecialchars($user['mobile']); ?>
                        </div>

                    </div>

                </div>

                <!-- ADDRESS -->
                <div class="col-12">

                    <div class="info-box">

                        <div class="info-icon">

                            <i class="bi bi-geo-alt"></i>

                        </div>

                        <div class="info-title">
                            Address
                        </div>

                        <div class="info-value">
                            <?php echo htmlspecialchars($user['address']); ?>
                        </div>

                    </div>

                </div>

            </div>

            <!-- BUTTON -->
            <div class="text-center mt-5">

                <a href="edit_profile.php"
                   class="btn btn-edit">

                    <i class="bi bi-pencil-square me-2"></i>

                    Edit Profile

                </a>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>