<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* ADD PORTFOLIO IMAGE */
if (isset($_POST['add_portfolio'])) {

    $caption = trim($_POST['caption']);

    if (!empty($_FILES['image']['name'])) {

        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetDir = "../uploads/portfolio/";
        $targetFile = $targetDir . $imageName;

        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);

        $stmt = $conn->prepare("INSERT INTO vendor_portfolio (vendor_id, image_path, caption) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $vendor_id, $imageName, $caption);
        $stmt->execute();

        header("Location: portfolio.php?success=added");
        exit();
    }
}

/* DELETE PORTFOLIO IMAGE */
if (isset($_GET['delete'])) {

    $portfolio_id = intval($_GET['delete']);

    // Get image path first (vendor protected)
    $stmt = $conn->prepare("SELECT image_path FROM vendor_portfolio WHERE portfolio_id=? AND vendor_id=?");
    $stmt->bind_param("ii", $portfolio_id, $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        unlink("../uploads/portfolio/" . $result['image_path']);

        $stmt = $conn->prepare("DELETE FROM vendor_portfolio WHERE portfolio_id=? AND vendor_id=?");
        $stmt->bind_param("ii", $portfolio_id, $vendor_id);
        $stmt->execute();
    }

    header("Location: portfolio.php?success=deleted");
    exit();
}

/* FETCH ONLY THIS VENDOR PORTFOLIO */
$stmt = $conn->prepare("SELECT * FROM vendor_portfolio WHERE vendor_id=? ORDER BY portfolio_id DESC");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$portfolio = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Vendor Portfolio</title>
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; font-family:'Segoe UI'; }

.sidebar {
    height:100vh;
    background:#1e1e2f;
    position:fixed;
}

.sidebar a {
    color:#c2c7d0;
    padding:14px 20px;
    display:block;
    text-decoration:none;
}

.sidebar a:hover, .sidebar .active {
    background:linear-gradient(90deg,#4e73df,#1cc88a);
    color:#fff;
}

.topbar {
    background:#fff;
    padding:18px 25px;
    box-shadow:0 3px 10px rgba(0,0,0,0.05);
}

.main-content {
    margin-left:16.6%;
}

.card-box {
    background:#fff;
    padding:25px;
    border-radius:15px;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
}

.portfolio-card {
    background:#fff;
    border-radius:15px;
    padding:15px;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    margin-bottom:20px;
}

.portfolio-img {
    width:100%;
    height:200px;
    object-fit:cover;
    border-radius:12px;
}

.btn-gradient {
    background:linear-gradient(135deg,#4e73df,#1cc88a);
    border:none;
    color:#fff;
    padding:8px 20px;
    border-radius:25px;
}
</style>
</head>

<body>
<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
    <div class="col-md-2 sidebar p-0">
        <h4 class="text-center py-4 border-bottom " style="color:white;">EventSpace</h4>

        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="card_profile.php">Profile</a>
        <a href="card_services.php">Upload templates</a>
        <a href="card_templates.php">My Templates</a>
        <a href="card_portfolio.php">Portfolio</a>
        <a href="card_availability.php">Availability</a>
        <a href="card_bookings.php">Bookings</a>
        <a href="card_earnings.php">Earnings</a>
        <a href="card_reviews.php">Reviews</a>
        <a href="../auth/logout.php" class="text-danger">Logout</a>
    </div>

<!-- Main -->
<div class="col-md-10 offset-md-2 main-content p-0">

<div class="topbar">
    <h5>Manage Portfolio</h5>
</div>

<div class="container mt-4">

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?= $_GET['success'] == 'added' ? 'Image Added Successfully!' : 'Image Deleted Successfully!'; ?>
    </div>
<?php endif; ?>

<!-- Add Portfolio -->
<div class="card-box mb-4">
    <h5>Add Portfolio Image</h5>
    <form method="POST" enctype="multipart/form-data" class="row g-3 mt-2">

        <div class="col-md-4">
            <input type="file" name="image" class="form-control" required>
        </div>

        <div class="col-md-6">
            <input type="text" name="caption" class="form-control" placeholder="Caption">
        </div>

        <div class="col-md-2 text-end">
            <button type="submit" name="add_portfolio" class="btn-gradient">
                Upload
            </button>
        </div>

    </form>
</div>

<!-- Portfolio Grid -->
<h5>Your Portfolio</h5>

<div class="row mt-3">

<?php if ($portfolio->num_rows > 0): ?>
<?php while($row = $portfolio->fetch_assoc()): ?>
<div class="col-md-4">
    <div class="portfolio-card">
        <img src="../uploads/portfolio/<?= htmlspecialchars($row['image_path']); ?>" class="portfolio-img">
        <p class="mt-2 text-muted"><?= htmlspecialchars($row['caption']); ?></p>
        <div class="text-end">
            <a href="portfolio.php?delete=<?= $row['portfolio_id']; ?>"
               onclick="return confirm('Are you sure?')"
               class="btn btn-danger btn-sm">
               Delete
            </a>
        </div>
    </div>
</div>
<?php endwhile; ?>
<?php else: ?>
    <div class="alert alert-info">No portfolio images uploaded yet.</div>
<?php endif; ?>

</div>
</div>
</div>
</div>
</div>
</body>
</html>