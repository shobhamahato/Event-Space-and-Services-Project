<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['vendor_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* ---------------- TOTAL REVIEWS ---------------- */
$stmt = $conn->prepare("
    SELECT COUNT(*) as total, AVG(rating) as avg_rating 
    FROM reviews 
    WHERE vendor_id=?
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

$total_reviews = $data['total'] ?? 0;
$average_rating = round($data['avg_rating'], 1);
if($average_rating == null) $average_rating = 0;

/* ---------------- FETCH REVIEWS ---------------- */
$stmt = $conn->prepare("
    SELECT * FROM reviews 
    WHERE vendor_id=? 
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Reviews</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f8f9fa; }
        .sidebar { height: 100vh; background: #212529; color: #fff; }
        .sidebar a {
            color: #adb5bd;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar .active {
            background: linear-gradient(135deg,#4e73df,#1cc88a);;
            color: #fff;
        }
        .topbar {
            background: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .card-box {
            border-radius: 15px;
        }
        .star {
            color: #ffc107;
            font-size: 18px;
        }
    </style>
</head>

<body>
<div class="container-fluid">
<div class="row">

    <!-- Sidebar -->
    <div class="col-md-2 sidebar p-0">
        <h4 class="text-center py-4 border-bottom">EventSpace</h4>
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="services.php">Services</a>
        <a href="packages.php">Packages</a>
        <a href="portfolio.php">Portfolio</a>
        <a href="availability.php">Availability</a>
        <a href="bookings.php">Bookings</a>
        <a href="earnings.php">Earnings</a>
        <a href="reviews.php" class="active">Reviews</a>
        <a href="../auth/logout.php" class="text-danger">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-10 p-0">

        <div class="topbar">
            <h5 class="mb-0">Customer Reviews</h5>
        </div>

        <div class="container mt-4">

            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card card-box shadow-sm p-3 text-center">
                        <h6>Total Reviews</h6>
                        <h3><?php echo $total_reviews; ?></h3>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-box shadow-sm p-3 text-center">
                        <h6>Average Rating</h6>
                        <h3>
                            <?php echo $average_rating; ?> ⭐
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="card shadow-sm p-4">
                <h5>All Reviews</h5>

                <?php if($reviews->num_rows > 0): ?>
                    <?php while($row = $reviews->fetch_assoc()): ?>
                        <div class="border rounded p-3 mb-3">
                            <div class="mb-2">
                                <?php
                                for($i=1; $i<=5; $i++){
                                    if($i <= $row['rating']){
                                        echo '<span class="star">★</span>';
                                    } else {
                                        echo '<span class="star text-secondary">★</span>';
                                    }
                                }
                                ?>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($row['comment']); ?></p>
                            <small class="text-muted">
                                Posted on <?php echo date("d M Y", strtotime($row['created_at'])); ?>
                            </small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No reviews yet.</p>
                <?php endif; ?>

            </div>

        </div>

    </div>
</div>
</div>
</body>
</html>