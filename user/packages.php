<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/db.php');

$packages = mysqli_query($conn,"SELECT * FROM packages");
if(!$packages){
    die("Database Query Failed: " . mysqli_error($conn));
}
?>

<?php include('../includes/user-navbar.php'); ?>

<div class="container">
    <h4>🎁 Packages</h4>
    <?php while($row = mysqli_fetch_assoc($packages)){
        $title = htmlspecialchars($row['title']);
        $price = htmlspecialchars($row['price']);
        $img = !empty($row['image']) ? $row['image'] : 'default.jpg';
    ?>
    <div class="card">
        <img src="../uploads/<?php echo $img; ?>">
        <h6><?php echo $title; ?></h6>
        <small>₹<?php echo $price; ?> onwards</small><br><br>
        <a href="checkout.php?id=<?php echo $row['id']; ?>&type=package" class="book-btn">Book Now</a>
    </div>
    <?php } ?>
</div>