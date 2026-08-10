<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/db.php');

$uid = $_SESSION['user_id'];

// get messages
$result = mysqli_query($conn,"SELECT * FROM messages WHERE receiver_id='$uid' ORDER BY id DESC");

include('../includes/user-navbar.php');
?>

<!DOCTYPE html>
<html>
<head>
<title>Messages</title>

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
box-shadow:0 2px 5px rgba(0,0,0,0.1);
}

.sender{
font-weight:bold;
color:#ff3f6c;
}
</style>

</head>
<body>

<div class="container">

<h3>💬 Messages</h3>

<?php
if(!$result || mysqli_num_rows($result)==0){
echo "<p>No messages.</p>";
}
else{

while($row = mysqli_fetch_assoc($result)){
?>

<div class="card">

<p class="sender">From User ID: <?php echo $row['sender_id']; ?></p>

<p><?php echo $row['message']; ?></p>

</div>

<?php
}
}
?>

</div>

</body>
</html>