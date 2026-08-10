<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}
include('../config/db.php');
$uid = $_SESSION['user_id'];

$sql = "SELECT co.*, p.title, p.price, p.image 
        FROM checkout co
        LEFT JOIN packages p ON co.item_id = p.id AND co.item_type='package'
        WHERE co.user_id='$uid'";
$orders = mysqli_query($conn, $sql);
if(!$orders){
    die("Database Query Failed: " . mysqli_error($conn));
}
?>

<?php include('../includes/user-navbar.php'); ?>

<div class="container">
<h4>📦 My Orders</h4>
<?php if(mysqli_num_rows($orders)==0){ ?>
<p>No orders found.</p>
<?php } else {
while($row = mysqli_fetch_assoc($orders)){
    $name = htmlspecialchars($row['title'] ?? 'Unknown Item');
    $price = htmlspecialchars($row['price'] ?? 0);
    $img = !empty($row['image']) ? $row['image'] : 'default.jpg';
?>
<div class="card">
<img src="../uploads/<?php echo $img; ?>" alt="<?php echo $name; ?>">
<div>
<h6><?php echo $name; ?></h6>
<small>₹<?php echo $price; ?></small>
</div>
</div>
<?php } } ?>
</div>