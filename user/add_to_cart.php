<?php
session_start();

/* DATABASE CONNECTION */

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "event_management_system"
);

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* LOGIN CHECK */

if(!isset($_SESSION['user_id'])){

    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* GET SERVICE ID */

if(!isset($_GET['service_id'])){

    header("Location: vendors.php");
    exit();
}

$service_id = $_GET['service_id'];

/* FETCH SERVICE */

$serviceQuery = mysqli_query($conn,"
    SELECT *
    FROM services
    WHERE service_id='$service_id'
");

if(mysqli_num_rows($serviceQuery) == 0){

    $_SESSION['cart_message'] = "Service not found";

    header("Location: vendors.php");
    exit();
}

$service = mysqli_fetch_assoc($serviceQuery);

/* GET VENDOR ID FROM SERVICE */

$vendor_id = $service['vendor_id'];

/* CHECK IF SERVICE ALREADY EXISTS IN CART */

$check = mysqli_query($conn,"
    SELECT *
    FROM cart
    WHERE user_id='$user_id'
    AND service_id='$service_id'
");

if(mysqli_num_rows($check) > 0){

    $_SESSION['cart_message'] =
    "Service already added to cart";

    header("Location: details.php?vendor_id=$vendor_id");
    exit();
}

/* INSERT INTO CART */

$insert = mysqli_query($conn,"
    INSERT INTO cart(
        user_id,
        vendor_id,
        service_id
    )

    VALUES(
        '$user_id',
        '$vendor_id',
        '$service_id'
    )
");

/* SUCCESS / ERROR MESSAGE */

if($insert){

    $_SESSION['cart_message'] =
    "Service added to cart successfully";

}else{

    $_SESSION['cart_message'] =
    "Something went wrong";
}

/* REDIRECT */

header("Location: cart.php");
exit();
?>