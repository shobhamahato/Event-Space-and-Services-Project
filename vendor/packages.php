<?php  
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['vendor_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* ---------------- ADD PACKAGE ---------------- */

if(isset($_POST['add_package'])){

    $package_name = $_POST['package_name'];
    $price        = $_POST['price'];
    $description  = $_POST['description'];

    /* PHOTO UPLOAD */

    $picture = "";

    if(isset($_FILES['picture']) && $_FILES['picture']['name'] != ""){

        $picture = time() . "_" . $_FILES['picture']['name'];

        move_uploaded_file(
            $_FILES['picture']['tmp_name'],
            "../uploads/packages/" . $picture
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO packages 
        (vendor_id, package_name, price, description, picture) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isdss",
        $vendor_id,
        $package_name,
        $price,
        $description,
        $picture
    );

    $stmt->execute();

    header("Location: packages.php");
    exit();
}

/* ---------------- DELETE PACKAGE ---------------- */

if(isset($_GET['delete'])){

    $package_id = $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM packages 
        WHERE package_id=? 
        AND vendor_id=?
    ");

    $stmt->bind_param("ii", $package_id, $vendor_id);

    $stmt->execute();

    header("Location: packages.php");
    exit();
}

/* ---------------- FETCH PACKAGES ---------------- */

$stmt = $conn->prepare("
    SELECT * FROM packages 
    WHERE vendor_id=? 
");

$stmt->bind_param("i", $vendor_id);

$stmt->execute();

$packages = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>

    <title>Vendor Packages</title>

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background: linear-gradient(135deg,#eef2ff,#f8fbff);
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

/* HEADER CARD */

.package-header{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    border-radius: 22px;
    padding: 35px;
    color: white;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

/* FORM CARD */

.form-card{
     background: linear-gradient(135deg,#faf5ff,#f5f3ff);
    border-radius: 22px;
    padding: 30px;
    border: 1px solid #ede9fe;

    box-shadow:
        0 10px 25px rgba(196,181,253,0.18),
        0 4px 10px rgba(221,214,254,0.12);
}

/* TABLE CARD */

.table-card{
    background: white;
    border-radius: 22px;
    padding: 30px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
}

/* INPUT */

.form-control{
     border-radius: 12px;
    padding: 12px 15px;
    border: 1px solid #e9e5ff;
    background: #fcfbff;
    transition: 0.3s;
}

.form-control:hover{
    border-color: #d8b4fe;
    background: #ffffff;
}

.form-control:focus{
   background: #ffffff;
    border-color: #c4b5fd;
    box-shadow: 0 0 0 0.15rem rgba(196,181,253,0.18);
}

/* FORM SECTION */

.form-section{
    background: rgba(255,255,255,0.75);
    padding: 18px;
    border-radius: 16px;
    border: 1px solid #ffe4e6;
}

/* BUTTON */

.btn-custom{
    background: linear-gradient(135deg,#5b86e5,#36d1dc);
    border: none;
    color: white;
    padding: 12px 28px;
    border-radius: 30px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-custom:hover{
    transform: translateY(-2px);
    opacity: 0.95;
    color: white;
}

/* TABLE */

table{
    border-radius: 15px;
    overflow: hidden;
}

thead{
    background: linear-gradient(135deg,#ff9966,#ff5e62);
    color: white;
}

table tbody tr:hover{
    background: #fff5f5;
}

/* DELETE BUTTON */

.btn-delete{
background: #ffeded;
    color: #dc3545;
    border-radius: 10px;
    padding: 6px 14px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.3s;
}

.btn-delete:hover{
    background: #dc3545;
    color: white;
}

/* IMAGE */

.package-img{
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 10px;
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
    <a href="packages.php" class="active">Packages</a>
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

    <div class="topbar">

        <h5 class="mb-0">
            Manage Packages
        </h5>

    </div>

    <div class="container mt-4">

        <!-- HEADER -->

        <div class="package-header mb-4">

            <h2>
                Vendor Packages 🎁
            </h2>

            <p class="mt-3 mb-0">
                Create attractive packages for your customers.
            </p>

        </div>

        <!-- ADD PACKAGE -->

        <div class="form-card mb-4">

            <h5 class="mb-4">
                Add New Package
            </h5>

            <form method="POST" enctype="multipart/form-data">

                <div class="row g-4">

                    <div class="col-md-4">

                        <div class="form-section">

                            <label class="mb-2">
                                Package Name
                            </label>

                            <input type="text"
                                   name="package_name"
                                   class="form-control"
                                   placeholder="Enter Package Name"
                                   required>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="form-section">

                            <label class="mb-2">
                                Price
                            </label>

                            <input type="number"
                                   step="0.01"
                                   name="price"
                                   class="form-control"
                                   placeholder="Price (₹)"
                                   required>

                        </div>

                    </div>

                    <div class="col-md-5">

                        <div class="form-section">

                            <label class="mb-2">
                                Description
                            </label>

                            <input type="text"
                                   name="description"
                                   class="form-control"
                                   placeholder="Package Description">

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-section">

                            <label class="mb-2">
                                Package Photo
                            </label>

                            <input type="file"
                                   name="picture"
                                   class="form-control">

                        </div>

                    </div>

                </div>

                <button type="submit"
                        name="add_package"
                        class="btn btn-custom mt-4">

                    Add Package

                </button>

            </form>

        </div>

        <!-- PACKAGES TABLE -->

        <div class="table-card">

            <h5 class="mb-4">
                Your Packages
            </h5>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>
                            <th>Photo</th>
                            <th>Package</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th width="150">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php while($row = $packages->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php if(!empty($row['picture'])): ?>
                                    <img src="../uploads/packages/<?php echo $row['picture']; ?>" class="package-img">
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['package_name']); ?>
                            </td>

                            <td>
                                ₹<?php echo number_format($row['price'],2); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['description']); ?>
                            </td>

                            <td>

                                <a href="packages.php?delete=<?php echo $row['package_id']; ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Delete this package?')">

                                    Delete

                                </a>

                            </td>

                        </tr>

                        <?php endwhile; ?>

                        <?php if($packages->num_rows == 0): ?>

                        <tr>

                            <td colspan="5" class="text-center py-4">
                                No packages added yet.
                            </td>

                        </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</div>
</div>

</body>
</html>