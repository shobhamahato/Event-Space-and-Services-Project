<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/db.php');

$venues = mysqli_query($conn,"SELECT * FROM venues ORDER BY id DESC");
?>

<?php include('../includes/user-navbar.php'); ?>

<style>
body{font-family:'Segoe UI',sans-serif;background:#f4f5f7;}
.container{padding:15px;}
.card{background:white;padding:10px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);margin-bottom:15px;}
.card img{width:100%;height:150px;object-fit:cover;border-radius:15px;margin-bottom:10px;}
</style>

<div class="container">
    <h4>🏛️ Venues</h4>

    <?php if(mysqli_num_rows($venues)==0){ ?>
        <p>No venues available.</p>
    <?php } else { ?>
        <?php while($row=mysqli_fetch_assoc($venues)){ ?>
            <div class="card">
                <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                <h6><?php echo htmlspecialchars($row['name']); ?></h6>
                <small>Location: <?php echo htmlspecialchars($row['location']); ?></small>
            </div>
        <?php } ?>
    <?php } ?>
</div>