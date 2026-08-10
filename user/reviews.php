<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/db.php');

$uid = $_SESSION['user_id'];

// get user reviews
$result = mysqli_query($conn,"SELECT * FROM reviews WHERE user_id='$uid' ORDER BY id DESC");

include('../includes/user-navbar.php');
?>

<!DOCTYPE html>
<html>
<head>

<title>My Reviews</title>

<style>

body{
font-family:Arial;
background:#f5f5f5;
margin:0;
}

.container{
width:90%;
margin:auto;
padding:20px;
}

.card{
background:white;
padding:12px;
margin-top:10px;
border-radius:8px;
box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

.rating{
color:#ffb400;
font-weight:bold;
}

</style>

</head>

<body>

<div class="container">

<h3>⭐ My Reviews</h3>

<?php
if(!$result || mysqli_num_rows($result)==0){
echo "<p>No reviews yet.</p>";
}
else{

while($row = mysqli_fetch_assoc($result)){
?>

<div class="card">

<h4><?php echo $row['item_type']; ?></h4>

<p class="rating">Rating: <?php echo $row['rating']; ?> ⭐</p>

<p><?php echo $row['comment']; ?></p>

</div>

<?php
}
}
?>

</div>

</body>
</html>