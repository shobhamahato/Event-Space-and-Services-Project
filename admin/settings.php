<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$message = "";

/* =========================
   CHANGE ADMIN PASSWORD
========================= */
if(isset($_POST['change_password'])){

    $current_password = trim($_POST['current_password']);
    $new_password     = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    $admin_id = $_SESSION['admin_id'];

    // FETCH ADMIN FROM DB
    $stmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if(!$admin){
        $message = "<div class='alert alert-danger'>Admin not found!</div>";
    }
    else{

        $db_password = $admin['password'];

        // CHECK CURRENT PASSWORD
        if($current_password !== $db_password){
            $message = "<div class='alert alert-danger'>Current password is incorrect.</div>";
        }

        // CHECK NEW PASSWORD MATCH
        elseif($new_password !== $confirm_password){
            $message = "<div class='alert alert-warning'>New passwords do not match.</div>";
        }

        else{

            // UPDATE PASSWORD
            $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password, $admin_id);

            if($stmt->execute()){
                $message = "<div class='alert alert-success'>Password updated successfully!</div>";
            }else{
                $message = "<div class='alert alert-danger'>Failed to update password.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Admin Settings</title>

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
    background: linear-gradient(to right, #eef2ff, #f8f9ff);
    padding: 18px 25px;
    border-bottom: 1px solid #e6ebf5;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.topbar h5{
    color: #2c3e50;
    font-weight: 600;
}

/* PAGE HEADER */
.page-header{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    border-radius:22px;
    padding:35px;
    color:white;
}

/* SETTINGS CARD */
.settings-card{
    background:white;
    border-radius:22px;
    padding:35px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

/* INPUT */
.form-control{
    border-radius:12px;
    padding:12px 14px;
    border:1px solid #dbe3f0;
}

.form-control:focus{
    border-color:#4b6cb7;
    box-shadow:0 0 0 0.15rem rgba(75,108,183,0.15);
}

/* BUTTON */
.btn-save{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    color:white;
    border:none;
    padding:12px 28px;
    border-radius:30px;
    font-weight:600;
}

.btn-save:hover{
    opacity:0.95;
    transform:translateY(-2px);
    color:white;
}

/* ALERT */
.alert{
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
Admin Panel
</h4>

<a href="dashboard.php">Dashboard</a>
<a href="vendors.php">Vendors</a>
<a href="users.php">Users</a>
<a href="bookings.php">Bookings</a>
<a href="reports.php">Reports</a>
<a href="settings.php" class="active">Settings</a>

<a href="../auth/logout.php" class="text-warning">
Logout
</a>

</div>

<!-- MAIN CONTENT -->
<div class="col-md-10 p-0">

<div class="topbar">
<h5 class="mb-0">Admin Settings</h5>
</div>

<div class="container mt-4">

<div class="page-header mb-4">
<h2>System Settings ⚙️</h2>
<p class="mt-3 mb-0">
Manage administrator account settings and security preferences.
</p>
</div>

<?php echo $message; ?>

<div class="settings-card">

<h4 class="mb-4">Change Password</h4>

<form method="POST">

<div class="mb-4">
<label class="form-label fw-semibold">Current Password</label>
<input type="password" name="current_password" class="form-control" required>
</div>

<div class="mb-4">
<label class="form-label fw-semibold">New Password</label>
<input type="password" name="new_password" class="form-control" required>
</div>

<div class="mb-4">
<label class="form-label fw-semibold">Confirm Password</label>
<input type="password" name="confirm_password" class="form-control" required>
</div>

<button type="submit" name="change_password" class="btn-save">
Update Password
</button>

</form>

<hr class="my-5">

<div class="row g-4">

<div class="col-md-6">
<div class="p-4 rounded-4 border bg-light h-100">

<h5 class="mb-3">Administrator Info</h5>

<div class="mb-3">
<label class="fw-semibold d-block mb-1">Admin Email</label>
<input type="text" class="form-control" value="admin123@gmail.com" readonly>
</div>

<div class="mb-3">
<label class="fw-semibold d-block mb-1">Role</label>
<input type="text" class="form-control" value="Super Admin" readonly>
</div>

<div>
<label class="fw-semibold d-block mb-1">Account Status</label>
<span class="badge bg-success px-3 py-2">Active</span>
</div>

</div>
</div>

<div class="col-md-6">
<div class="p-4 rounded-4 border bg-light h-100">

<h5 class="mb-3">System Information</h5>

<div class="d-flex justify-content-between mb-3">
<span>Platform</span>
<span class="fw-semibold">EventSpace</span>
</div>

<div class="d-flex justify-content-between mb-3">
<span>Version</span>
<span class="fw-semibold">v1.0</span>
</div>

<div class="d-flex justify-content-between mb-3">
<span>Server Date</span>
<span class="fw-semibold"><?php echo date("d M Y"); ?></span>
</div>

<div class="d-flex justify-content-between">
<span>Security</span>
<span class="badge bg-primary">Protected</span>
</div>

</div>
</div>

</div>

</div>

</div>
</div>
</div>

</body>
</html>