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

    $stmt = $conn->prepare("INSERT INTO services (vendor_id, service_name, price, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $vendor_id, $service_name, $price, $description);
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

/* ---------------- EDIT SERVICE ---------------- */
if(isset($_POST['update_service'])){
    $service_id  = $_POST['service_id'];
    $service_name = $_POST['service_name'];
    $price        = $_POST['price'];
    $description  = $_POST['description'];

    $stmt = $conn->prepare("UPDATE services SET service_name=?, price=?, description=? WHERE service_id=? AND vendor_id=?");
    $stmt->bind_param("sdsii", $service_name, $price, $description, $service_id, $vendor_id);
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
        <a href="services.php" class="active">Services</a>
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

        <div class="topbar">
            <h5 class="mb-0">Manage Services</h5>
        </div>

        <div class="container mt-4">

            <!-- Add Service Form -->
            <div class="card shadow-sm p-4 mb-4">
                <h5>Add New Service</h5>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <input type="text" name="service_name" class="form-control" placeholder="Service Name" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="Price (₹)" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <input type="text" name="description" class="form-control" placeholder="Description">
                        </div>
                    </div>
                    <button type="submit" name="add_service" class="btn" style="background : linear-gradient(135deg,#4e73df,#1cc88a);color:white;">Add Service</button>
                </form>
            </div>

            <!-- Services Table -->
            <div class="card shadow-sm p-4">
                <h5>Your Services</h5>
                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $services->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                            <td>₹<?php echo number_format($row['price'],2); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a href="services.php?delete=<?php echo $row['service_id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this service?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($services->num_rows == 0): ?>
                        <tr>
                            <td colspan="4" class="text-center">No services added yet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
</div>
</body>
</html>