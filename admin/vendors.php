<?php 
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

/* APPROVE */

if(isset($_GET['approve'])){

    $id = (int)$_GET['approve'];

    $stmt = $conn->prepare("UPDATE vendors SET status='approved' WHERE vendor_id=?");

    $stmt->bind_param("i",$id);

    $stmt->execute();

    header("Location: vendors.php");

    exit();
}

/* REJECT */

if(isset($_GET['reject'])){

    $id = (int)$_GET['reject'];

    $stmt = $conn->prepare("UPDATE vendors SET status='rejected' WHERE vendor_id=?");

    $stmt->bind_param("i",$id);

    $stmt->execute();

    header("Location: vendors.php");

    exit();
}

/* DELETE VENDOR */

if(isset($_GET['delete'])){

    $id = (int)$_GET['delete'];

    /* FETCH VENDOR TYPE */

    $typeQuery = $conn->prepare("SELECT vendor_type FROM vendors WHERE vendor_id=?");

    $typeQuery->bind_param("i",$id);

    $typeQuery->execute();

    $vendor = $typeQuery->get_result()->fetch_assoc();

    if($vendor){

        try{

            /* DELETE FROM TYPE TABLE */

            if($vendor['vendor_type'] == 'decorator'){

                $stmt = $conn->prepare("DELETE FROM decorators WHERE vendor_id=?");
                $stmt->bind_param("i",$id);
                $stmt->execute();
            }

            elseif($vendor['vendor_type'] == 'caterer'){

                $stmt = $conn->prepare("DELETE FROM caterers WHERE vendor_id=?");
                $stmt->bind_param("i",$id);
                $stmt->execute();
            }

            elseif($vendor['vendor_type'] == 'beauty_parlour'){

                $stmt = $conn->prepare("DELETE FROM beauty_parlours WHERE vendor_id=?");
                $stmt->bind_param("i",$id);
                $stmt->execute();
            }

            elseif($vendor['vendor_type'] == 'venue'){

                $stmt = $conn->prepare("DELETE FROM venues WHERE vendor_id=?");
                $stmt->bind_param("i",$id);
                $stmt->execute();
            }

            elseif($vendor['vendor_type'] == 'card_vendor'){

                $stmt = $conn->prepare("DELETE FROM cards WHERE vendor_id=?");
                $stmt->bind_param("i",$id);
                $stmt->execute();
            }

            elseif($vendor['vendor_type'] == 'photography'){

                $stmt = $conn->prepare("DELETE FROM photography_vendors WHERE vendor_id=?");
                $stmt->bind_param("i",$id);
                $stmt->execute();
            }

            /* DELETE RELATED TABLES */

            $tables = [
                "services",
                "vendor_portfolio",
                "availability",
                "bookings",
                "reviews"
            ];

            foreach($tables as $table){

                $query = "DELETE FROM $table WHERE vendor_id=?";

                $stmt = $conn->prepare($query);

                if($stmt){

                    $stmt->bind_param("i",$id);

                    $stmt->execute();
                }
            }

            /* FINALLY DELETE MAIN VENDOR */

            $stmt = $conn->prepare("DELETE FROM vendors WHERE vendor_id=?");

            $stmt->bind_param("i",$id);

            $stmt->execute();

        }catch(Exception $e){

            echo "Error : ".$e->getMessage();
        }
    }

    header("Location: vendors.php");

    exit();
}

/* FETCH VENDORS */

$result = $conn->query("SELECT * FROM vendors ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Vendors</title>

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

    box-shadow:
        0 4px 12px rgba(0,0,0,0.05),
        0 2px 4px rgba(0,0,0,0.03);

    position: relative;
    z-index: 100;
}

.topbar h5{
    color: #2c3e50;
    font-weight: 600;
    letter-spacing: 0.3px;
}

/* HEADER */

.page-header{
    background: linear-gradient(135deg,#4b6cb7,#182848);
    border-radius:22px;
    padding:35px;
    color:white;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

/* TABLE CARD */

.table-card{
    background:white;
    border-radius:22px;
    padding:25px;

    box-shadow:
    0 10px 25px rgba(196,181,253,0.12),
    0 4px 10px rgba(221,214,254,0.08);
}

/* TABLE */

.table{
    border-radius:15px;
    overflow:hidden;
}

.table thead{
    background:linear-gradient(135deg,#4b6cb7,#182848);
    color:white;
}

.table thead th{
    border:none;
    padding:16px;
    font-weight:600;
    white-space:nowrap;
}

.table tbody tr{
    transition:0.3s;
}

.table tbody tr:hover{
    background:#f8f7ff;
}

.table td{
    padding:16px;
    vertical-align:middle;
}

/* BUTTONS */

.action-buttons{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}

.action-buttons .btn{
    border-radius:25px;
    padding:6px 16px;
    font-size:13px;
    font-weight:500;
    min-width:90px;
}

/* BADGES */

.badge{
    padding:8px 12px;
    border-radius:20px;
    font-size:12px;
}

/* DELETE BUTTON */

.btn-outline-danger{
    border-width:2px;
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

<a href="vendors.php" class="active">Vendors</a>

<a href="users.php">Users</a>

<a href="bookings.php">Bookings</a>

<a href="reports.php">Reports</a>

<a href="settings.php">Settings</a>

<a href="../auth/logout.php" class="text-warning">
Logout
</a>

</div>

<!-- MAIN CONTENT -->

<div class="col-md-10 p-0">

<!-- TOPBAR -->

<div class="topbar">

<h5 class="mb-0">
Manage Vendors
</h5>

</div>

<div class="container mt-4">

<!-- HEADER -->

<div class="page-header mb-4">

<h2>
Vendor Management 👨‍💼
</h2>

<p class="mt-3 mb-0">
Approve, reject and manage all registered vendors professionally.
</p>

</div>

<!-- TABLE SECTION -->

<div class="table-card">

<div class="table-responsive">

<table class="table align-middle">

<thead>

<tr>

<th>ID</th>

<th>Business</th>

<th>Owner</th>

<th>Type</th>

<th>Email</th>

<th>Status</th>

<th width="300">
Action
</th>

</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()): ?>

<tr>

<td>
<?php echo $row['vendor_id']; ?>
</td>

<td>
<?php echo htmlspecialchars($row['business_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['owner_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['vendor_type']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['email']); ?>
</td>

<td>

<?php if($row['status']=='pending'): ?>

<span class="badge bg-warning text-dark">
Pending
</span>

<?php elseif($row['status']=='approved'): ?>

<span class="badge bg-success">
Approved
</span>

<?php else: ?>

<span class="badge bg-danger">
Rejected
</span>

<?php endif; ?>

</td>

<td>

<div class="action-buttons">

<?php if($row['status']=='pending'): ?>

<a href="?approve=<?php echo $row['vendor_id']; ?>"
class="btn btn-success">

Approve

</a>

<a href="?reject=<?php echo $row['vendor_id']; ?>"
class="btn btn-danger">

Reject

</a>

<?php endif; ?>

<a href="?delete=<?php echo $row['vendor_id']; ?>"
class="btn btn-outline-danger"
onclick="return confirm('Are you sure you want to delete this vendor?');">

Delete

</a>

</div>

</td>

</tr>

<?php endwhile; ?>

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