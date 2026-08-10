
<?php
session_start();
require_once("../config/db.php");

// Check if vendor logged in
if(!isset($_SESSION['vendor_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* ---------------- FETCH VENDOR DETAILS ---------------- */

$stmt = $conn->prepare("SELECT business_name, vendor_type, email FROM vendors WHERE vendor_id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

$business_name = $vendor['business_name'];
$vendor_type   = $vendor['vendor_type'];
$email         = $vendor['email'];

/* ---------------- FETCH PROFILE IMAGE ---------------- */

/* ---------------- FETCH PROFILE IMAGE ---------------- */

$stmt = $conn->prepare("
    SELECT image_path 
    FROM vendor_portfolio 
    WHERE vendor_id = ? 
    ORDER BY created_at DESC 
    LIMIT 1
");

$stmt->bind_param("i", $vendor_id);
$stmt->execute();

$image_result = $stmt->get_result();
$image_data = $image_result->fetch_assoc();

/* PROFILE IMAGE PATH */

if(!empty($image_data['image_path'])){

    $profile_image = "../uploads/portfolio/" . $image_data['image_path'];

}else{

    // DEFAULT AVATAR
    $profile_image = "https://ui-avatars.com/api/?name="
                    . urlencode($business_name) .
                    "&background=4b6cb7&color=fff";
}


/* ---------------- DASHBOARD COUNTS ---------------- */

// Total Bookings
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE vendor_id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$total_bookings = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Total Earnings
$stmt = $conn->prepare("
    SELECT SUM(amount) as total 
    FROM bookings 
    WHERE vendor_id=? 
    AND payment_status='Paid'
");

$stmt->bind_param("i", $vendor_id);

$stmt->execute();

$total_earnings = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

if($total_earnings == null){
    $total_earnings = 0;
}

// Services Count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM services WHERE vendor_id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$total_services = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Reviews Count
// Customer Feedback Count

$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM bookings
    WHERE vendor_id = ?
    AND rating IS NOT NULL
    AND review IS NOT NULL
    AND review != ''
");

$stmt->bind_param("i", $vendor_id);
$stmt->execute();

$total_reviews = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
/* ---------------- DYNAMIC PROGRESS CALCULATIONS ---------------- */

// BOOKINGS GROWTH

$booking_progress = 0;

if($total_bookings > 0){

    // 50 bookings = 100%
    $booking_progress = min(($total_bookings / 50) * 100, 100);
}


// CUSTOMER SATISFACTION

$stmt = $conn->prepare("
    SELECT AVG(rating) as avg_rating
    FROM bookings
    WHERE vendor_id = ?
    AND rating IS NOT NULL
");

$stmt->bind_param("i", $vendor_id);
$stmt->execute();

$avg_rating = $stmt->get_result()->fetch_assoc()['avg_rating'] ?? 0;

// 1 star = 20%
$rating_percentage = $avg_rating * 20;


// SERVICE AVAILABILITY

$service_progress = 0;

if($total_services > 0){

    // 10 services = 100%
    $service_progress = min(($total_services / 10) * 100, 100);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Vendor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background: #f4f7fc;
            font-family: 'Segoe UI', sans-serif;
        }

        /* SIDEBAR */

        .sidebar{
            min-height: 100vh;
            background: linear-gradient(180deg,#182848,#4b6cb7);
            color: white;
        }

        .sidebar h4{
            font-weight: bold;
            letter-spacing: 1px;
        }

        .sidebar a{
            color: #dfe6f1;
            text-decoration: none;
            display: block;
            padding: 14px 22px;
            margin: 6px 10px;
            border-radius: 10px;
            transition: 0.3s;
            font-size: 15px;
        }

        .sidebar a:hover{
            background: rgba(255,255,255,0.15);
            color: white;
            transform: translateX(4px);
        }

        .sidebar .active{
            background: white;
            color: #2c3e50;
            font-weight: 600;
        }

      
/* TOPBAR */


.topbar{
    background: linear-gradient(to right, #eef2ff, #f8f9ff);
    padding: 18px 25px;
    border-bottom: 1px solid #e6ebf5;

    /* SOFT PROFESSIONAL SHADOW */
    box-shadow:
        0 4px 12px rgba(0,0,0,0.05),
        0 2px 4px rgba(0,0,0,0.03);

    position: relative;
    z-index: 100;
}

.topbar h5{
    color: #2c3e50;
    font-weight: 600;
    letter-spacing: 0.3px;
}

/* VENDOR BADGE */

.vendor-badge{
    background: linear-gradient(to right, #dfe8ff, #edf2ff);
    color: #355c9a !important;
    border: 1px solid #c9d8ff;
    border-radius: 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
    padding: 10px 18px !important;
}

.vendor-badge:hover{
    background: linear-gradient(to right, #355c9a, #4b6cb7);
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(53,92,154,0.25);
}

        /* WELCOME BANNER */

        .welcome-banner{
            background: linear-gradient(135deg,#4b6cb7,#182848);
            color: white;
            border-radius: 18px;
            padding: 35px;
        }

        .welcome-banner h2{
            font-weight: bold;
        }
        

        /* PROFILE PHOTO */

        .profile-photo{
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,0.4);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* DASHBOARD CARDS */

        .dashboard-card{
            border: none;
            border-radius: 18px;
            padding: 25px;
            background: white;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .dashboard-card:hover{
            transform: translateY(-5px);
        }

        .card-icon{
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        /* ICON COLORS */

        .icon-blue{
            background: #e8f0ff;
            color: #4b6cb7;
        }

        .icon-green{
            background: #e7fff1;
            color: #1cc88a;
        }

        .icon-orange{
            background: #fff3e6;
            color: #f39c12;
        }

        .icon-red{
            background: #ffeaea;
            color: #e74a3b;
        }

        /* ANALYTICS SECTION */

        .analytics-card{
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .progress{
            height: 10px;
            border-radius: 20px;
        }

        table tr td,
        table tr th{
            padding: 12px !important;
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

        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="services.php">Services</a>
        <a href="packages.php">Packages</a>
        <a href="portfolio.php">Portfolio</a>
        <!-- <a href="availability.php">Availability</a> -->
        <a href="bookings.php">Bookings</a>
        <a href="earnings.php">Earnings</a>
        <a href="reviews.php">Reviews</a>

        <a href="../auth/logout.php" class="text-warning">
            Logout
        </a>

    </div>

    <!-- MAIN CONTENT -->

    <div class="col-md-10 p-0">

        <!-- TOPBAR -->

        <div class="topbar d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Welcome, <?php echo htmlspecialchars($business_name); ?>
            </h5>

            <span class="badge px-3 py-2 text-uppercase"
                  style="background:linear-gradient(135deg,#4b6cb7,#182848);">

                <?php echo htmlspecialchars($vendor_type); ?>

            </span>

        </div>

        <!-- DASHBOARD CONTENT -->

        <div class="container mt-4">

            <!-- WELCOME BANNER -->

            <div class="welcome-banner mb-4">

                <div class="row align-items-center">

                    <!-- LEFT CONTENT -->

                    <div class="col-md-8">

                        <h2>
                            Hello, <?php echo htmlspecialchars($business_name); ?> 👋
                        </h2>

                        <p class="mt-3">
                            Manage your bookings, services, earnings and grow your event business easily.
                        </p>

                        <span class="badge bg-light text-dark px-3 py-2 mt-2">
                            <?php echo ucfirst($vendor_type); ?> Vendor
                        </span>

                    </div>

                    <!-- PROFILE PHOTO -->

                     <div class="col-md-4 text-center">

                        <img 
                            src="<?php echo $profile_image; ?>" 
                            class="profile-photo"
                            alt="Vendor Profile"
                        >
                    </div>

                </div>

            </div>

            <!-- DASHBOARD CARDS -->

            <div class="row g-4">

                <!-- BOOKINGS -->

                <div class="col-md-3">

                    <div class="dashboard-card">

                        <div class="card-icon icon-blue">
                            📅
                        </div>

                        <h6>Total Bookings</h6>

                        <h3 class="fw-bold">
                            <?php echo $total_bookings; ?>
                        </h3>

                        <small class="text-success">
                            +12% this month
                        </small>

                    </div>

                </div>

                <!-- EARNINGS -->

                <div class="col-md-3">

                    <div class="dashboard-card">

                        <div class="card-icon icon-green">
                            ₹
                        </div>

                        <h6>Total Earnings</h6>

                        <h3 class="fw-bold">
                            ₹<?php echo number_format($total_earnings,2); ?>
                        </h3>

                        <small class="text-success">
                            Revenue increasing
                        </small>

                    </div>

                </div>

                <!-- SERVICES -->

                <div class="col-md-3">

                    <div class="dashboard-card">

                        <div class="card-icon icon-orange">
                            🎉
                        </div>

                        <h6>Services</h6>

                        <h3 class="fw-bold">
                            <?php echo $total_services; ?>
                        </h3>

                        <small class="text-primary">
                            Active services
                        </small>

                    </div>

                </div>

                <!-- REVIEWS -->

                <div class="col-md-3">

                    <div class="dashboard-card">

                        <div class="card-icon icon-red">
                            ⭐
                        </div>

                        <h6>Reviews</h6>

                        <h3 class="fw-bold">
                            <?php echo $total_reviews; ?>
                        </h3>

                        <small class="text-warning">
                            Customer feedback
                        </small>

                    </div>

                </div>

            </div>

            <!-- ANALYTICS SECTION -->

            <div class="row mt-5">

                <!-- PROGRESS SECTION -->

                <div class="col-md-6 mb-4">

                    <div class="analytics-card">

                        <h5 class="mb-4">
                            Monthly Booking Progress
                        </h5>

                       <p class="mb-2">
                        Bookings Growth 
                        (<?php echo round($booking_progress); ?>%)
                    </p>

                    <div class="progress mb-4">

                        <div class="progress-bar bg-primary"
                            style="width:<?php echo $booking_progress; ?>%">
                        </div>

                        </div>
                        <p class="mb-2">
                            Customer Satisfaction
                            (<?php echo round($avg_rating,1); ?>/5 ⭐)
                        </p>

                        <div class="progress mb-4">

                            <div class="progress-bar bg-success"
                                style="width:<?php echo $rating_percentage; ?>%">
                            </div>

                        </div>

                                    <p class="mb-2">
                    Service Availability
                    (<?php echo round($service_progress); ?>%)
                </p>

                <div class="progress">

                    <div class="progress-bar bg-warning"
                        style="width:<?php echo $service_progress; ?>%">
                    </div>

                </div>

                    </div>

                </div>

                <!-- VENDOR INFO -->

                <div class="col-md-6 mb-4">

                    <div class="analytics-card">

                        <h5 class="mb-4">
                            Vendor Information
                        </h5>

                        <table class="table">

                            <tr>
                                <th>Business Name</th>
                                <td><?php echo $business_name; ?></td>
                            </tr>

                            <tr>
                                <th>Email</th>
                                <td><?php echo $email; ?></td>
                            </tr>

                            <tr>
                                <th>Vendor Type</th>
                                <td><?php echo ucfirst($vendor_type); ?></td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-success">
                                        Active
                                    </span>
                                </td>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
</div>

</body>
</html>
