<?php
session_start();

$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* LOGIN CHECK */

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* GET VENDOR */

$vendor_id = $_POST['vendor_id'] ?? null;

if(!$vendor_id){
    die("Invalid Request");
}

/* FETCH VENDOR */

$vendorQuery = mysqli_query($conn,"
    SELECT *
    FROM vendors
    WHERE vendor_id='$vendor_id'
");

$vendor = mysqli_fetch_assoc($vendorQuery);

if(!$vendor){
    die("Vendor Not Found");
}

/* GET SERVICE DATA */

$service_id    = $_POST['service_id'] ?? '';
$service_name  = $_POST['service_name'] ?? '';
$service_price = $_POST['service_price'] ?? 0;

$cart_id = $_POST['cart_id'] ?? '';

/* CUSTOMER DETAILS */

$customer_name  = $_POST['customer_name'] ?? '';
$customer_phone = $_POST['customer_phone'] ?? '';
$customer_email = $_POST['customer_email'] ?? '';

/* EVENT DETAILS */

$event_date     = $_POST['event_date'] ?? '';

$start_time     = $_POST['start_time'] ?? '';
$end_time       = $_POST['end_time'] ?? '';

$event_time = $start_time . " To " . $end_time;

$event_location = $_POST['event_location'] ?? '';

$guest_count    = $_POST['guest_count'] ?? '';

$special_request = $_POST['requirements'] ?? '';

/* EXTRA INPUTS */

$plates        = $_POST['count'] ?? 0;

$card_count    = $_POST['card_count'] ?? 0;

$beauty_count  = $_POST['beauty_person'] ?? 0;

$vendor_type = strtolower($vendor['vendor_type']);

/* ================= FINAL AMOUNT ================= */

$final_amount = 0;

/* CATERER */

if($vendor_type == "caterer"){

    $final_amount = $plates * $service_price;

}

/* CARDS */

else if($vendor_type == "card_vendor"){

    $final_amount = $card_count * $service_price;

}

/* BEAUTY */

else if($vendor_type == "beauty"){

    $final_amount = $beauty_count * $service_price;

}

/* OTHER VENDORS */

else{

    $final_amount = $service_price;

}

/* ================= CONFIRM BOOKING ================= */

if(isset($_POST['submit_booking'])){

    $insert = mysqli_query($conn,"
        INSERT INTO bookings(
            user_id,
            vendor_id,
            service_id,
            service_name,
            customer_name,
            customer_email,
            customer_phone,
            event_date,
            event_time,
            event_location,
            guest_count,
            special_request,
            amount,
            payment_status,
            booking_status
        )
        VALUES(
            '$user_id',
            '$vendor_id',
            '$service_id',
            '$service_name',
            '$customer_name',
            '$customer_email',
            '$customer_phone',
            '$event_date',
            '$event_time',
            '$event_location',
            '$guest_count',
            '$special_request',
            '$final_amount',
            'Pending',
            'Pending'
        )
    ");

    if($insert){

        /* REMOVE ITEM FROM CART */

        if(!empty($cart_id)){

            mysqli_query($conn,"
                DELETE FROM cart
                WHERE cart_id='$cart_id'
                AND user_id='$user_id'
            ");

        }

        header("Location: orders.php");
        exit();

    }else{

        die("Booking Failed : " . mysqli_error($conn));

    }

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Booking Preview</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f7fb;
    font-family:Poppins;
}

.card-box{
    background:#fff;
    padding:30px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

.section-title{
    font-weight:700;
    margin-top:20px;
    color:#5a189a;
}

.info{
    margin-bottom:8px;
}

.btn-submit{
    background:linear-gradient(135deg,#ff758f,#c77dff);
    border:none;
    padding:12px;
    color:#fff;
    font-weight:600;
    border-radius:12px;
}

.btn-back{
    background:#4a4e69;
    color:#fff;
    padding:10px 18px;
    border-radius:10px;
    text-decoration:none;
}

</style>

</head>

<body>

<div class="container mt-5 mb-5">

<div class="card-box">

<h3 class="text-center mb-4">📋 Booking Preview</h3>

<!-- VENDOR -->

<h5 class="section-title">Vendor Details</h5>

<div class="info">
<b>Business:</b>
<?php echo $vendor['business_name']; ?>
</div>

<div class="info">
<b>Service:</b>
<?php echo $service_name; ?>
</div>

<div class="info">
<b>Type:</b>
<?php echo ucfirst($vendor_type); ?>
</div>

<hr>

<!-- CUSTOMER -->

<h5 class="section-title">Customer Details</h5>

<div class="info">
<b>Name:</b>
<?php echo $customer_name; ?>
</div>

<div class="info">
<b>Phone:</b>
<?php echo $customer_phone; ?>
</div>

<div class="info">
<b>Email:</b>
<?php echo $customer_email; ?>
</div>

<hr>

<!-- EVENT -->

<h5 class="section-title">Event Details</h5>

<div class="info">
<b>Date:</b>
<?php echo $event_date; ?>
</div>

<div class="info">
<b>Time:</b>
<?php echo $event_time; ?>
</div>

<div class="info">
<b>Location:</b>
<?php echo $event_location; ?>
</div>

<div class="info">
<b>Guests:</b>
<?php echo $guest_count; ?>
</div>

<hr>

<!-- PRICE -->

<h5 class="section-title">Service Breakdown</h5>

<?php if($vendor_type == "caterer"){ ?>

<div class="info">
Plates: <?php echo $plates; ?> × ₹<?php echo $service_price; ?>
</div>

<?php } ?>

<?php if($vendor_type == "cards"){ ?>

<div class="info">
Cards: <?php echo $card_count; ?> × ₹<?php echo $service_price; ?>
</div>

<?php } ?>

<?php if($vendor_type == "beauty"){ ?>

<div class="info">
Persons: <?php echo $beauty_count; ?> × ₹<?php echo $service_price; ?>
</div>

<?php } ?>

<?php if(
    $vendor_type != "caterer" &&
    $vendor_type != "cards" &&
    $vendor_type != "beauty"
){ ?>

<div class="info">
Base Price: ₹<?php echo $service_price; ?>
</div>

<?php } ?>

<hr>

<h4>
Total Amount: ₹<?php echo number_format($final_amount,2); ?>
</h4>

<hr>

<!-- CONFIRM BOOKING -->

<form method="POST">

<input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">

<input type="hidden" name="service_id" value="<?php echo $service_id; ?>">

<input type="hidden" name="service_name" value="<?php echo $service_name; ?>">

<input type="hidden" name="service_price" value="<?php echo $service_price; ?>">

<input type="hidden" name="cart_id" value="<?php echo $cart_id; ?>">

<input type="hidden" name="customer_name" value="<?php echo $customer_name; ?>">

<input type="hidden" name="customer_phone" value="<?php echo $customer_phone; ?>">

<input type="hidden" name="customer_email" value="<?php echo $customer_email; ?>">

<input type="hidden" name="event_date" value="<?php echo $event_date; ?>">

<input type="hidden" name="start_time" value="<?php echo $start_time; ?>">

<input type="hidden" name="end_time" value="<?php echo $end_time; ?>">

<input type="hidden" name="event_location" value="<?php echo $event_location; ?>">

<input type="hidden" name="guest_count" value="<?php echo $guest_count; ?>">

<input type="hidden" name="requirements" value="<?php echo $special_request; ?>">

<input type="hidden" name="count" value="<?php echo $plates; ?>">

<input type="hidden" name="card_count" value="<?php echo $card_count; ?>">

<input type="hidden" name="beauty_person" value="<?php echo $beauty_count; ?>">

<button type="submit"
        name="submit_booking"
        class="btn btn-submit w-100">

    Send Booking Request

</button>

</form>

<div class="text-center mt-3">

<a href="javascript:history.back()"
   class="btn-back">

← Edit Details

</a>

</div>

</div>

</div>

</body>
</html>