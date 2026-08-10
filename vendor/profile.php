
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

/* FETCH PROFILE IMAGE */
$stmt = $conn->prepare("
    SELECT image_path 
    FROM vendor_portfolio 
    WHERE vendor_id=? 
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();

$image_result = $stmt->get_result();
$image_data = $image_result->fetch_assoc();

$profile_image = $image_data['image_path'] ?? 'default.png';

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
    /* PROFILE CARD */

.profile-card{
    background: linear-gradient(135deg,#fdfbff,#f3f8ff);
    border-radius: 20px;
    padding: 35px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    border: 1px solid #edf1f7;
}


/* FORM SECTION BOX */

.form-section{
    background: linear-gradient(135deg,#ffffff,#f6faff);
    padding: 18px;
    border-radius: 16px;
    border: 1px solid #e8eef8;
    transition: 0.3s;
}

.form-section:hover{
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.04);
}


/* INPUT FIELD */

.form-control{
    border-radius: 12px;
    padding: 12px 15px;
    border: 1px solid #dbe4f0;
    background: #f9fbff;
    transition: 0.3s;
}

.form-control:focus{
    background: #ffffff;
    border-color: #6c8cff;
    box-shadow: 0 0 0 0.15rem rgba(108,140,255,0.15);
}


/* SECTION TITLE */

.section-title{
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
    border-left: 5px solid #6c8cff;
    padding-left: 12px;
}


/* LABEL */



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
    background: white;
    padding: 18px 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

/* PROFILE HEADER */

.profile-header{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    border-radius: 18px;
    padding: 35px;
    color: white;
}

.profile-image{
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.4);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* PROFILE CARD */
/* PROFILE CARD */

.profile-card{
    background: linear-gradient(135deg,#faf5ff,#f5f3ff);
    border-radius: 22px;
    padding: 35px;
    border: 1px solid #ede9fe;

    box-shadow:
        0 10px 25px rgba(196,181,253,0.18),
        0 4px 10px rgba(221,214,254,0.12);
}


/* FORM SECTION */

.form-section{
    background: rgba(255,255,255,0.75);
    padding: 18px;
    border-radius: 16px;
    border: 1px solid #ede9fe;
    transition: 0.3s;

    box-shadow:
        0 4px 10px rgba(221,214,254,0.10);
}


/* FORM SECTION HOVER */

.form-section:hover{
    transform: translateY(-3px);
    background: rgba(255,255,255,0.95);
    box-shadow: 0 4px 12px rgba(196,181,253,0.18);
    border-color: #ddd6fe;
}


/* INPUT FIELD */

.form-control{
    border-radius: 12px;
    padding: 12px 15px;
    border: 1px solid #e9e5ff;
    background: #fcfbff;
    transition: 0.3s;
}


/* INPUT HOVER */

.form-control:hover{
    border-color: #d8b4fe;
    background: #ffffff;
}


/* INPUT FOCUS */

.form-control:focus{
    background: #ffffff;
    border-color: #c4b5fd;
    box-shadow: 0 0 0 0.15rem rgba(196,181,253,0.18);
}

/* SECTION TITLE */

.section-title{
    font-size: 18px;
    font-weight: 600;
    color: #182848;
    margin-bottom: 20px;
    border-left: 5px solid #4b6cb7;
    padding-left: 12px;
}

/* FORM */

.form-control{
    border-radius: 10px;
    padding: 12px;
    border: 1px solid #dbe4f0;
}

.form-control:focus{
    box-shadow: none;
    border-color: #4b6cb7;
}

label{
    font-weight: 500;
    margin-bottom: 8px;
}

/* BUTTON */

.btn-custom{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    border: none;
    color: white;
    padding: 12px 28px;
    border-radius: 30px;
    font-weight: 500;
    transition: 0.3s;
}

.btn-custom:hover{
    transform: translateY(-2px);
    opacity: 0.95;
    color: white;
}

/* BADGE */

.vendor-badge{
    background: rgba(255,255,255,0.2);
    padding: 8px 20px;
    border-radius: 25px;
    display: inline-block;
    margin-top: 12px;
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
    <a href="profile.php" class="active">Profile</a>
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

    <div class="topbar">
        <h5 class="mb-0">
            Vendor Profile
        </h5>
    </div>

    <div class="container mt-4">

        <?php if (isset($_GET['success'])): ?>

            <div class="alert alert-success">
                Profile updated successfully!
            </div>

        <?php endif; ?>

        <!-- PROFILE HEADER -->

        <div class="profile-header mb-4">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h2>
                        <?php echo htmlspecialchars($vendor['business_name']); ?>
                    </h2>

                    <p class="mt-3 mb-0">
                        Manage and update your business profile information.
                    </p>

                    <div class="vendor-badge">
                        <?php echo ucfirst($vendor_type); ?> Vendor
                    </div>

                </div>

                <div class="col-md-4 text-center">

                    <img 
                        src="../uploads/portfolio/<?php echo $profile_image; ?>" 
                        class="profile-image"
                        alt="Profile Image"
                    >

                </div>

            </div>

        </div>

        <!-- PROFILE FORM -->

        <div class="profile-card">

            <form method="POST" class="row g-4">

                <!-- BASIC INFO -->

                <div class="section-title">
                    Basic Information
                </div>

                <div class="col-md-6">

                    <label>Business Name</label>

                    <input type="text" 
                           name="business_name" 
                           class="form-control"
                           value="<?= htmlspecialchars($vendor['business_name']); ?>" 
                           required>

                </div>

                <div class="col-md-6">

                    <label>Owner Name</label>

                    <input type="text" 
                           name="owner_name" 
                           class="form-control"
                           value="<?= htmlspecialchars($vendor['owner_name']); ?>" 
                           required>

                </div>

                <div class="col-md-6">

                    <label>Phone</label>

                    <input type="text" 
                           name="phone" 
                           class="form-control"
                           value="<?= htmlspecialchars($vendor['phone']); ?>" 
                           required>

                </div>

                <!-- BUSINESS DETAILS -->

                <div class="section-title mt-4">
                    Business Details
                </div>

                <div class="col-md-6">

                    <label>Street</label>

                    <input type="text" 
                           name="street" 
                           class="form-control"
                           value="<?= htmlspecialchars($details['street'] ?? ''); ?>">

                </div>

                <div class="col-md-6">

                    <label>City</label>

                    <input type="text" 
                           name="city" 
                           class="form-control"
                           value="<?= htmlspecialchars($details['city'] ?? ''); ?>">

                </div>

                <div class="col-md-6">

                    <label>Pincode</label>

                    <input type="text" 
                           name="pincode" 
                           class="form-control"
                           value="<?= htmlspecialchars($details['pincode'] ?? ''); ?>">

                </div>

                <div class="col-md-6">

                    <label>Experience (Years)</label>

                    <input type="number" 
                           name="experience" 
                           class="form-control"
                           value="<?= htmlspecialchars($details['experience'] ?? ''); ?>">

                </div>

                <div class="col-12">

                    <label>About Business</label>

                    <textarea 
                        name="about_business" 
                        class="form-control" 
                        rows="4"><?= htmlspecialchars($details['about_business'] ?? ''); ?></textarea>

                </div>

                <!-- BUTTON -->

                <div class="col-12 text-end mt-3">

                    <button type="submit" 
                            name="update_profile" 
                            class="btn-custom">

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
