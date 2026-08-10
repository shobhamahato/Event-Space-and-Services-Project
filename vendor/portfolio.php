
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

/* FETCH PORTFOLIO */

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

body{
    background: linear-gradient(135deg,#eef2ff,#f8fbff);
    font-family:'Segoe UI',sans-serif;
}

/* SIDEBAR */

.sidebar{
    min-height:100vh;
    background: linear-gradient(180deg,#182848,#4b6cb7);
    color:white;
}

.sidebar h4{
    font-weight:bold;
    letter-spacing:1px;
}

.sidebar a{
    color:#dfe6f1;
    text-decoration:none;
    display:block;
    padding:14px 22px;
    margin:6px 10px;
    border-radius:10px;
    transition:0.3s;
    font-size:15px;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.15);
    color:white;
    transform:translateX(4px);
}

.sidebar .active{
    background:white;
    color:#2c3e50;
    font-weight:600;
}

/* TOPBAR */

.topbar{
    background:white;
    padding:18px 25px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

/* HEADER */

.portfolio-header{
  background: linear-gradient(135deg,#4b6cb7,#182848);
    border-radius:22px;
    padding:35px;
    color:white;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

/* FORM CARD */

.form-card{
    background: linear-gradient(135deg,#faf5ff,#f5f3ff);
    border-radius:22px;
    padding:30px;
    border:1px solid #ede9fe;

    box-shadow:
    0 10px 25px rgba(196,181,253,0.18),
    0 4px 10px rgba(221,214,254,0.12);
}

/* FORM SECTION */

.form-section{
    background:rgba(255,255,255,0.75);
    padding:18px;
    border-radius:16px;
    border:1px solid #ede9fe;
    transition:0.3s;

    box-shadow:
    0 4px 10px rgba(221,214,254,0.10);
}

.form-section:hover{
    transform:translateY(-3px);
    background:rgba(255,255,255,0.95);
    box-shadow:0 4px 12px rgba(196,181,253,0.18);
    border-color:#ddd6fe;
}

/* INPUT */

.form-control{
    border-radius:12px;
    padding:12px 15px;
    border:1px solid #e9e5ff;
    background:#fcfbff;
    transition:0.3s;
}

.form-control:hover{
    border-color:#d8b4fe;
    background:#ffffff;
}

.form-control:focus{
    background:#ffffff;
    border-color:#c4b5fd;
    box-shadow:0 0 0 0.15rem rgba(196,181,253,0.18);
}

/* BUTTON */

.btn-gradient{
    background:linear-gradient(135deg,#5b86e5,#36d1dc);
    border:none;
    color:#fff;
    padding:10px 28px;
    border-radius:30px;
    font-weight:600;
    transition:0.3s;
}

.btn-gradient:hover{
    transform:translateY(-2px);
    opacity:0.95;
    color:white;
}

/* PORTFOLIO CARD */

.portfolio-card{
    background:white;
    border-radius:20px;
    padding:15px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
    transition:0.3s;
    margin-bottom:25px;
}

.portfolio-card:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

/* IMAGE */

.portfolio-img{
    width:100%;
    height:240px;
    object-fit:cover;
    border-radius:16px;
}

/* CAPTION */

.caption{
    color:#6b7280;
    margin-top:12px;
    font-size:15px;
}

/* DELETE BUTTON */

.btn-delete{
    background:#ffeded;
    color:#dc3545;
    border:none;
    padding:7px 16px;
    border-radius:10px;
    text-decoration:none;
    transition:0.3s;
    font-size:14px;
}

.btn-delete:hover{
    background:#dc3545;
    color:white;
}

/* ALERT */

.alert-success{
    border:none;
    border-radius:12px;
    background:#e9fff2;
    color:#198754;
}

.alert-info{
    border:none;
    border-radius:12px;
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

<a href="dashboard.php">Dashboard</a>
<a href="profile.php">Profile</a>
<a href="services.php">Services</a>
<a href="packages.php">Packages</a>
<a href="portfolio.php" class="active">Portfolio</a>
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

<div class="topbar">

<h5 class="mb-0">
Manage Portfolio
</h5>

</div>

<div class="container mt-4">

<?php if (isset($_GET['success'])): ?>

<div class="alert alert-success">

<?= $_GET['success'] == 'added' ? 'Image Added Successfully!' : 'Image Deleted Successfully!'; ?>

</div>

<?php endif; ?>

<!-- HEADER -->

<div class="portfolio-header mb-4">

<h2>
Portfolio Gallery ✨
</h2>

<p class="mt-3 mb-0">
Upload your best event moments and showcase your creativity professionally.
</p>

</div>

<!-- ADD IMAGE FORM -->

<div class="form-card mb-4">

<h5 class="mb-4">
Add Portfolio Image
</h5>

<form method="POST" enctype="multipart/form-data">

<div class="row g-4">

<div class="col-md-4">

<div class="form-section">

<label class="mb-2">
Choose Image
</label>

<input type="file"
       name="image"
       class="form-control"
       required>

</div>

</div>

<div class="col-md-6">

<div class="form-section">

<label class="mb-2">
Image Caption
</label>

<input type="text"
       name="caption"
       class="form-control"
       placeholder="Enter image caption">

</div>

</div>

<div class="col-md-2 d-flex align-items-end">

<button type="submit"
        name="add_portfolio"
        class="btn-gradient w-100">

Upload

</button>

</div>

</div>

</form>

</div>

<!-- PORTFOLIO GRID -->

<h4 class="mb-4">
Your Portfolio
</h4>

<div class="row">

<?php if ($portfolio->num_rows > 0): ?>

<?php while($row = $portfolio->fetch_assoc()): ?>

<div class="col-md-4">

<div class="portfolio-card">

<img src="../uploads/portfolio/<?= htmlspecialchars($row['image_path']); ?>"
     class="portfolio-img">

<p class="caption">

<?= htmlspecialchars($row['caption']); ?>

</p>

<div class="text-end mt-3">

<a href="portfolio.php?delete=<?= $row['portfolio_id']; ?>"
   onclick="return confirm('Are you sure?')"
   class="btn-delete">

Delete

</a>

</div>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="alert alert-info">
No portfolio images uploaded yet.
</div>

<?php endif; ?>

</div>

</div>
</div>
</div>
</div>

</body>
</html>
