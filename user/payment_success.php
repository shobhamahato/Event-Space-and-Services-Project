<?php

session_start();

$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* CHECK BOOKING ID */

if(!isset($_POST['booking_id'])){
    die("Booking ID Missing");
}

$booking_id = $_POST['booking_id'];

/* FETCH BOOKING */

$booking_query = mysqli_query($conn,
"SELECT * FROM bookings WHERE id='$booking_id'");

if(mysqli_num_rows($booking_query) == 0){
    die("Booking Not Found");
}

$booking = mysqli_fetch_assoc($booking_query);

$vendor_id = $booking['vendor_id'];

$amount = $booking['amount'];

/* UPDATE PAYMENT STATUS */

mysqli_query($conn,"
UPDATE bookings
SET 
payment_status='Paid',
booking_status='Confirmed'
WHERE id='$booking_id'
");

/* SEND AMOUNT TO VENDOR WALLET */

// mysqli_query($conn,"
// UPDATE vendors
// SET wallet_balance = wallet_balance + '$amount'
// WHERE vendor_id='$vendor_id'
// ");

?>

<!DOCTYPE html>
<html>
<head>

<title>Payment Successful</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:linear-gradient(135deg,#d8f3dc,#b7e4c7);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Poppins,sans-serif;
}

.success-box{
    background:white;
    padding:50px;
    border-radius:25px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
    max-width:500px;
    width:100%;
}

.check{
    font-size:80px;
    margin-bottom:20px;
}

h1{
    color:#2d6a4f;
    font-weight:700;
}

p{
    color:#666;
    margin-top:15px;
}

.btn-custom{
    margin-top:30px;
    background:#2d6a4f;
    color:white;
    border:none;
    padding:14px 30px;
    border-radius:12px;
    text-decoration:none;
    display:inline-block;
    font-weight:600;
}

.btn-custom:hover{
    background:#1b4332;
    color:white;
}

</style>

</head>

<body>

<div class="success-box">

    <div class="check">
        ✅
    </div>

    <h1>Payment Successful</h1>

    <p>

        Your payment has been completed successfully.
        Vendor has received the payment.

    </p>

    <h3 class="mt-4">

        ₹<?php echo number_format($amount,2); ?>

    </h3>

    <a href="orders.php" class="btn-custom">

        Back To Booking History

    </a>

</div>

</body>
</html>