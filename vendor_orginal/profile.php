<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* FETCH COMMON VENDOR INFO */
$stmt = $conn->prepare("SELECT * FROM vendors WHERE vendor_id=?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$vendor = $stmt->get_result()->fetch_assoc();

$vendor_type = $vendor['vendor_type'];

/* MAP VENDOR TYPE TO TABLE */
$tables = [
    "decorator" => "decorators",
    "caterer" => "caterers",
    "photography" => "photography_vendors",
    "beauty_parlour" => "beauty_parlours",
    "music_vendor" => "music_vendors",
    "card_vendor"=>"cards",
    "venue"=>"venues"
];

$table = $tables[$vendor_type] ?? null;

if (!$table) {
    die("Invalid vendor type.");
}

/* FETCH VENDOR SPECIFIC DETAILS */
$query = "SELECT * FROM $table WHERE vendor_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$details = $stmt->get_result()->fetch_assoc();

/* UPDATE PROFILE */
if (isset($_POST['update_profile'])) {

    $business_name = $_POST['business_name'];
    $owner_name = $_POST['owner_name'];
    $phone = $_POST['phone'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $experience = $_POST['experience'];
    $about_business = $_POST['about_business'];

    $stmt = $conn->prepare("UPDATE vendors SET business_name=?, owner_name=?, phone=? WHERE vendor_id=?");
    $stmt->bind_param("sssi", $business_name, $owner_name, $phone, $vendor_id);
    $stmt->execute();

    $query = "UPDATE $table 
              SET street=?, city=?, pincode=?, experience=?, about_business=? 
              WHERE vendor_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssisi", $street, $city, $pincode, $experience, $about_business, $vendor_id);
    $stmt->execute();

    header("Location: profile.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Profile</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
}

/* Sidebar */
.sidebar {
    height: 100vh;
    background: #1e1e2f;
    color: #fff;
    position: fixed;
}

.sidebar h4 {
    font-weight: 600;
}

.sidebar a {
    color: #c2c7d0;
    padding: 14px 20px;
    display: block;
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a:hover, .sidebar .active {
    background: linear-gradient(90deg, #4e73df, #1cc88a);
    color: #fff;
}

/* Topbar */
.topbar {
    background: #ffffff;
    padding: 18px 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

/* Main */
.main-content {
    margin-left: 16.6%;
}

/* Card */
.profile-card {
    background: #ffffff;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}

/* Section title */
.section-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    border-left: 5px solid #4e73df;
    padding-left: 10px;
}

/* Vendor type badge */
.vendor-badge {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: #fff;
    padding: 8px 18px;
    border-radius: 25px;
    font-size: 14px;
    display: inline-block;
    margin-bottom: 20px;
}

/* Buttons */
.btn-custom {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    border: none;
    padding: 10px 25px;
    color: #fff;
    border-radius: 30px;
    font-weight: 500;
    transition: 0.3s;
}

.btn-custom:hover {
    opacity: 0.9;
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
    <a href="profile.php" class="active">Profile</a>
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
<div class="col-md-10 offset-md-2 main-content p-0">

<div class="topbar">
    <h5 class="mb-0">Vendor Profile</h5>
</div>

<div class="container mt-4">

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Profile updated successfully!</div>
<?php endif; ?>

<div class="profile-card">

    <div class="vendor-badge">
        <?= ucfirst($vendor_type); ?> Vendor
    </div>

    <form method="POST" class="row g-4">

        <!-- Basic Info -->
        <div class="section-title">Basic Information</div>

        <div class="col-md-6">
            <label>Business Name</label>
            <input type="text" name="business_name" class="form-control"
                   value="<?= htmlspecialchars($vendor['business_name']); ?>" required>
        </div>

        <div class="col-md-6">
            <label>Owner Name</label>
            <input type="text" name="owner_name" class="form-control"
                   value="<?= htmlspecialchars($vendor['owner_name']); ?>" required>
        </div>

        <div class="col-md-6">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control"
                   value="<?= htmlspecialchars($vendor['phone']); ?>" required>
        </div>

        <!-- Business Details -->
        <div class="section-title mt-4">Business Details</div>

        <div class="col-md-6">
            <label>Street</label>
            <input type="text" name="street" class="form-control"
                   value="<?= htmlspecialchars($details['street'] ?? ''); ?>">
        </div>

        <div class="col-md-6">
            <label>City</label>
            <input type="text" name="city" class="form-control"
                   value="<?= htmlspecialchars($details['city'] ?? ''); ?>">
        </div>

        <div class="col-md-6">
            <label>Pincode</label>
            <input type="text" name="pincode" class="form-control"
                   value="<?= htmlspecialchars($details['pincode'] ?? ''); ?>">
        </div>

        <div class="col-md-6">
            <label>Experience (Years)</label>
            <input type="number" name="experience" class="form-control"
                   value="<?= htmlspecialchars($details['experience'] ?? ''); ?>">
        </div>

        <div class="col-12">
            <label>About Business</label>
            <textarea name="about_business" class="form-control" rows="4"><?= htmlspecialchars($details['about_business'] ?? ''); ?></textarea>
        </div>

        <div class="col-12 text-end mt-3">
            <button type="submit" name="update_profile" class="btn-custom">
                Update Profile
            </button>
        </div>

    </form>

</div>
</div>
</div>
</div>
</div>

</body>
</html>