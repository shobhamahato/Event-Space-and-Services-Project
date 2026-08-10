<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['vendor_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$message = "";

/* ADD DATE */
if (isset($_POST['add_date'])) {
    $date = $_POST['available_date'];

    $stmt = $conn->prepare("INSERT INTO availability (vendor_id, available_date) VALUES (?, ?)");
    $stmt->bind_param("is", $vendor_id, $date);

    if ($stmt->execute()) {
        $message = "Date added successfully!";
    } else {
        $message = "Date already exists!";
    }
}

/* DELETE DATE */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM availability WHERE id=? AND vendor_id=?");
    $stmt->bind_param("ii", $id, $vendor_id);
    $stmt->execute();
    header("Location: availability.php");
    exit();
}

/* FETCH DATES */
$result = $conn->prepare("SELECT * FROM availability WHERE vendor_id=? ORDER BY available_date ASC");
$result->bind_param("i", $vendor_id);
$result->execute();
$dates = $result->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Availability</title>
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

        <a href="card_dashboard.php" class="active">Dashboard</a>
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

    <!-- Main Content -->
    <div class="col-md-10 p-0">

        <div class="topbar">
            <h5 class="mb-0">Manage Availability</h5>
        </div>

        <div class="container mt-4">

            <!-- Add Date Card -->
            <div class="card shadow-sm p-4 mb-4 card-box">
                <h5>Add Available Date</h5>

                <?php if($message != ""): ?>
                    <div class="alert alert-info mt-3">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="row g-3 mt-2">
                    <div class="col-md-4">
                        <input type="date" name="available_date" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="add_date" class="btn btn-success w-100">
                            Add Date
                        </button>
                    </div>
                </form>
            </div>

            <!-- Available Dates Table -->
            <div class="card shadow-sm p-4 card-box">
                <h5>Available Dates</h5>

                <table class="table table-bordered table-hover mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($dates->num_rows > 0): ?>
                            <?php while($row = $dates->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date("d M Y", strtotime($row['available_date'])); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $row['id']; ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Remove this date?')">
                                           Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center">No dates added yet.</td>
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