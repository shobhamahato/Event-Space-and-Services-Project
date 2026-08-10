
<?php 
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['vendor_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

/* ---------------- ADD SERVICE ---------------- */

if(isset($_POST['add_service'])){

    $service_name = $_POST['service_name'];
    $price        = $_POST['price'];
    $description  = $_POST['description'];

    /* PHOTO UPLOAD */

    $picture = "";

    if(isset($_FILES['picture']) && $_FILES['picture']['name'] != ""){

        $picture = time() . "_" . $_FILES['picture']['name'];

        move_uploaded_file(
            $_FILES['picture']['tmp_name'],
            "../uploads/services/" . $picture
        );
    }

    $stmt = $conn->prepare("INSERT INTO services (vendor_id, service_name, price, description, picture) VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("isdss", $vendor_id, $service_name, $price, $description, $picture);

    $stmt->execute();

    header("Location: services.php");
    exit();
}

/* ---------------- DELETE SERVICE ---------------- */

if(isset($_GET['delete'])){

    $service_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM services WHERE service_id=? AND vendor_id=?");

    $stmt->bind_param("ii", $service_id, $vendor_id);

    $stmt->execute();

    header("Location: services.php");
    exit();
}

/* ---------------- FETCH SERVICES ---------------- */

$stmt = $conn->prepare("SELECT * FROM services WHERE vendor_id=? ORDER BY created_at DESC");

$stmt->bind_param("i", $vendor_id);

$stmt->execute();

$services = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>

    <title>Vendor Services</title>

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

.service-header{
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

/* FORM FIELD BOX */

.form-section{
    background: rgba(255,255,255,0.75);
    padding: 18px;
    border-radius: 16px;
    border: 1px solid #ede9fe;
    transition: 0.3s;

    box-shadow:
        0 4px 10px rgba(221,214,254,0.10);
}

.form-section:hover{
    transform: translateY(-3px);
    background: rgba(255,255,255,0.95);
    box-shadow: 0 4px 12px rgba(196,181,253,0.18);
    border-color: #ddd6fe;
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
    background: linear-gradient(135deg,#5b86e5,#36d1dc);
    color: white;
}

table tbody tr:hover{
    background: #f8f5ff;
    transition: 0.3s;
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

.service-img{
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
        <a href="services.php" class="active">Services</a>
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
                Manage Services
            </h5>

        </div>

        <div class="container mt-4">

            <!-- HEADER -->

            <div class="service-header mb-4">

                <h2>
                    Vendor Services 🎉
                </h2>

                <p class="mt-3 mb-0">
                    Add and manage your event services professionally.
                </p>

            </div>

            <!-- ADD SERVICE FORM -->

            <div class="form-card mb-4">

                <h5 class="mb-4">
                    Add New Service
                </h5>

                <form method="POST" enctype="multipart/form-data">

                    <div class="row g-4">

                        <div class="col-md-4">

                            <div class="form-section">

                                <label class="mb-2">
                                    Service Name
                                </label>

                                <input type="text"
                                       name="service_name"
                                       class="form-control"
                                       placeholder="Enter Service Name"
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
                                       placeholder="Service Description">

                            </div>

                        </div>

                        <!-- PHOTO -->

                        <div class="col-md-4">

                            <div class="form-section">

                                <label class="mb-2">
                                    Service Photo
                                </label>

                                <input type="file"
                                       name="picture"
                                       class="form-control">

                            </div>

                        </div>

                    </div>

                    <button type="submit"
                            name="add_service"
                            class="btn btn-custom mt-4">

                        Add Service

                    </button>

                </form>

            </div>

            <!-- SERVICES TABLE -->

            <div class="table-card">

                <h5 class="mb-4">
                    Your Services
                </h5>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>
                                <th>Photo</th>
                                <th>Service</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th width="150">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php while($row = $services->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php if(!empty($row['picture'])): ?>
                                        <img src="../uploads/services/<?php echo $row['picture']; ?>" class="service-img">
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['service_name']); ?>
                                </td>

                                <td>
                                    ₹<?php echo number_format($row['price'],2); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </td>

                                <td>

                                    <a href="services.php?delete=<?php echo $row['service_id']; ?>"
                                       class="btn-delete"
                                       onclick="return confirm('Delete this service?')">

                                        Delete

                                    </a>

                                </td>

                            </tr>

                            <?php endwhile; ?>

                            <?php if($services->num_rows == 0): ?>

                            <tr>

                                <td colspan="5" class="text-center py-4">
                                    No services added yet.
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