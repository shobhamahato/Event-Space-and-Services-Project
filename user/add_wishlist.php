<?php
session_start();

$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
    echo "login_required";
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['vendor_id'])){

    $vendor_id = $_POST['vendor_id'];

    /* CHECK EXIST */
    $check = mysqli_query($conn,
    "SELECT * FROM wishlist
     WHERE user_id='$user_id'
     AND vendor_id='$vendor_id'");

    /* REMOVE */
    if(mysqli_num_rows($check) > 0){

        mysqli_query($conn,
        "DELETE FROM wishlist
         WHERE user_id='$user_id'
         AND vendor_id='$vendor_id'");

        echo "removed";

    }else{

        /* ADD */
        mysqli_query($conn,
        "INSERT INTO wishlist(user_id,vendor_id)
         VALUES('$user_id','$vendor_id')");

        echo "added";
    }
}
?>