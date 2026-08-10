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

/* ---------------- DASHBOARD COUNTS ---------------- */

// Total Bookings
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE vendor_id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$total_bookings = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Total Earnings (Only completed bookings)
$stmt = $conn->prepare("SELECT SUM(total_amount) as earnings FROM bookings WHERE vendor_id = ? AND status = 'completed'");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$total_earnings = $stmt->get_result()->fetch_assoc()['earnings'] ?? 0;

if($total_earnings == null){
    $total_earnings = 0;
}

// Services Count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM services WHERE vendor_id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$total_services = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Reviews Count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM reviews WHERE vendor_id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$total_reviews = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; }
        .sidebar { height: 100vh; background: #212529; color: #fff; }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }
        .sidebar a:hover { background: #343a40; color: #fff; }
        .sidebar .active { background: linear-gradient(135deg,#4e73df,#1cc88a);; color: #fff; }
        .topbar {
            background: #ffffff;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .card-box { border-radius: 15px; }
    </style>
</head>

<body>

<div class="container-fluid">
<div class="row">

    <!-- Sidebar -->

    <div class="col-md-2 sidebar p-0">
        <h4 class="text-center py-4 border-bottom">EventSpace</h4>

        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="services.php">Services</a>
        <a href="packages.php">Packages</a>
        <a href="portfolio.php">Portfolio</a>
        <a href="availability.php">Availability</a>
        <a href="bookings.php">Bookings</a>
        <a href="earnings.php">Earnings</a>
        <a href="reviews.php">Reviews</a>
        <a href="../auth/logout.php" class="text-danger">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-10 p-0">

        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Welcome, <?php echo htmlspecialchars($business_name); ?></h5>
            <span class="badge  text-uppercase" style="background:linear-gradient(135deg,#4e73df,#1cc88a);">
                <?php echo htmlspecialchars($vendor_type); ?>
            </span>
        </div>

        <!-- Dashboard Content -->
        <div class="container mt-4">

            <div class="row g-4">

                <div class="col-md-3">
                    <div class="card card-box shadow-sm p-3 text-center">
                        <h6>Total Bookings</h6>
                        <h3><?php echo $total_bookings; ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-box shadow-sm p-3 text-center">
                        <h6>Total Earnings</h6>
                        <h3>₹<?php echo number_format($total_earnings, 2); ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-box shadow-sm p-3 text-center">
                        <h6>Services</h6>
                        <h3><?php echo $total_services; ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card card-box shadow-sm p-3 text-center">
                        <h6>Reviews</h6>
                        <h3><?php echo $total_reviews; ?></h3>
                    </div>
                </div>

            </div>

            <!-- Vendor Type Section -->
            <div class="card shadow-sm mt-5 p-4">
                <h5>Your Vendor Type:</h5>
                <p class="mb-0">
                    You are registered as a 
                    <strong><?php echo ucfirst($vendor_type); ?></strong> vendor.
                </p>
            </div>

        </div>

    </div>
</div>
</div>

</body>
</html>