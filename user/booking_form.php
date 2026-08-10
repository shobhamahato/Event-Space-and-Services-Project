<?php
session_start();

$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* USER LOGIN CHECK */
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
/* FETCH USER DETAILS */
$userQuery = mysqli_query($conn,"
SELECT *
FROM users
WHERE id='$user_id'
");

$user = mysqli_fetch_assoc($userQuery);
/* GET REQUIRED DATA */
if(
    !isset($_GET['vendor_id']) ||
    !isset($_GET['service_id'])
){
    header("Location: cart.php");
    exit();
}

$vendor_id  = $_GET['vendor_id'];
$service_id = $_GET['service_id'];
$cart_id    = $_GET['cart_id'] ?? null;

/* FETCH VENDOR */
$query = mysqli_query($conn,"
    SELECT *
    FROM vendors
    WHERE vendor_id='$vendor_id'
");

$vendor = mysqli_fetch_assoc($query);

if(!$vendor){
    die("Vendor Not Found");
}

/* FETCH CORRECT SERVICE */
$serviceQuery = mysqli_query($conn,"
    SELECT *
    FROM services
    WHERE service_id='$service_id'
");

$service = mysqli_fetch_assoc($serviceQuery);

if(!$service){
    die("Service Not Found");
}

/* SERVICE DETAILS */
$service_name  = $service['service_name'];

/* GET AMOUNT FROM CART */
$service_price = $_GET['amount'] ?? $service['price'];

/* DISPLAY PRICE */
$display_price = $service_price;
?>

<!DOCTYPE html>
<html>
<head>

<title>Booking Form</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* (YOUR EXISTING CSS KEPT SAME — NO CHANGE) */
body{
    font-family:'Poppins',sans-serif;
    background: linear-gradient(135deg,#fad2e1,#cdb4db,#bde0fe);
    min-height:100vh;
}
.form-box{background:rgba(255,255,255,0.88);padding:45px;border-radius:30px;}
.btn-submit{
    background:linear-gradient(135deg,#ff758f,#c77dff);
    border:none;
    padding:15px;
    border-radius:16px;
    color:white;
    font-weight:600;
}
.booking-container{
    padding:40px 0;
}

.form-box{
    background:rgba(255,255,255,0.85);
    backdrop-filter:blur(18px);
    padding:45px;
    border-radius:30px;
    box-shadow:0 20px 50px rgba(181,126,220,0.18);
}

.form-control{
    border:none;
    background:#f8f0ff;
    border-radius:14px;
    padding:14px;
}

.form-control:focus{
    box-shadow:none;
    border:2px solid #c77dff;
    background:white;
}

.section-heading{
    font-size:22px;
    font-weight:700;
    margin:25px 0 15px;
    color:#2b2d42;
}

.btn-dark{
    background:linear-gradient(135deg,#ff8fab,#b185db);
    border:none;
    border-radius:14px;
    padding:10px 22px;
    font-weight:600;
}

.btn-dark:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(181,126,220,0.25);
}
</style>

</head>

<body>

<div class="container booking-container">
<div class="row justify-content-center">
<div class="col-lg-9">

<div class="form-box">
<div class="mb-4 d-flex justify-content-between align-items-center">

    <a href="details.php?vendor_id=<?php echo $vendor_id; ?>"
       class="btn btn-dark">

        ← Back

    </a>

    <h2 class="mb-0">
        Book <?php echo $vendor['business_name']; ?>
    </h2>

</div>
<h2>Book <?php echo $vendor['business_name']; ?></h2>

<form method="POST" action="preview.php">

<!-- HIDDEN DATA -->
<input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">
<input type="hidden" name="service_id" value="<?php echo $service_id; ?>">

<input type="hidden" name="service_name" value="<?php echo $service_name; ?>">

<input type="hidden" name="service_price" value="<?php echo $service_price; ?>">

<input type="hidden" name="cart_id" value="<?php echo $cart_id; ?>">
<input type="hidden" name="vendor_type" value="<?php echo $vendor['vendor_type']; ?>">

<!-- PERSONAL DETAILS -->
<div class="mb-3">
<input type="text"
       name="customer_name"
       class="form-control"
       placeholder="Full Name"
       value="<?php echo htmlspecialchars($user['name']); ?>"
       required>
</div>

<div class="mb-3">
<input type="text"
       name="customer_phone"
       class="form-control"
       placeholder="Phone"
       value="<?php echo htmlspecialchars($user['mobile']); ?>"
       required>
</div>

<div class="mb-3">
<input type="email"
       name="customer_email"
       class="form-control"
       placeholder="Email"
       value="<?php echo htmlspecialchars($user['email']); ?>"
       required>
</div>

<div class="mb-3">
<select name="event_type" class="form-control" required>
<option value="">Event Type</option>
<option>Party</option>
<option>Wedding</option>
<option>Birthday</option>
<option>Corporate</option>
<option>Reception</option>
</select>
</div>

<!-- EVENT DETAILS -->
<div class="mb-3">
<input type="date" name="event_date" class="form-control" required>
</div>

<div class="mb-3">
<input type="number" name="guest_count" class="form-control" placeholder="Guest Count" required>
</div>

<div class="mb-3">
<input type="text" name="event_location" class="form-control" placeholder="Location" required>
</div>

<div class="mb-3">
<textarea name="requirements" class="form-control" placeholder="Special Requirements"></textarea>
</div>

<div class="mb-3">
<input type="time" name="start_time" class="form-control" required>
</div>

<div class="mb-3">
<input type="time" name="end_time" class="form-control" required>
</div>
<div class="mb-3 p-3" style="background:#f3f0ff;border-radius:12px;">
    <strong>Service Amount:</strong>
    ₹<?php echo number_format($display_price,2); ?>
</div>
<!-- SERVICE SPECIFIC FIELDS -->
<!-- SERVICE DETAILS -->
<div class="section-heading">
Service Details (<?php echo ucfirst($vendor['vendor_type']); ?>)
</div>

<div class="row">

<?php if($vendor['vendor_type']=="decorator"){ ?>

    <div class="col-md-6 mb-3">
        <input name="decorator[theme]" class="form-control" placeholder="Theme">
    </div>

    <div class="col-md-6 mb-3">
        <input name="decorator[color]" class="form-control" placeholder="Color">
    </div>

    <div class="col-md-6 mb-3">
        <input name="decorator[flower]" class="form-control" placeholder="Flower Type">
    </div>

<?php } ?>


<?php if($vendor['vendor_type']=="caterer"){ ?>

    <div class="col-md-12 mb-3 p-2">
        <input type="number" name="count" class="form-control" placeholder="number of Plates required" min="0" required>
    </div>

<?php } ?>


<?php if($vendor['vendor_type']=="venue"){ ?>

    <div class="col-md-6 mb-3">
        <input name="venue[type]" class="form-control" placeholder="Venue Type">
    </div>

    <div class="col-md-6 mb-3">
        <input type="number" name="venue[capacity]" class="form-control" placeholder="Capacity">
    </div>

    <div class="col-md-6 mb-3">
        <input name="venue[location_type]" class="form-control" placeholder="Indoor / Outdoor">
    </div>

<?php } ?>


<?php if($vendor['vendor_type']=="music"){ ?>

    <div class="col-md-6 mb-3">
        <input name="music[type]" class="form-control" placeholder="Music Type (DJ / Band)">
    </div>

    <div class="col-md-6 mb-3">
        <input name="music[duration]" class="form-control" placeholder="Duration (hours)">
    </div>

    <div class="col-md-6 mb-3">
        <input name="music.sound_system" class="form-control" placeholder="Sound System Needed? (Yes/No)">
    </div>

<?php } ?>


<?php if($vendor['vendor_type']=="photography"){ ?>

    <div class="col-md-6 mb-3">
        <input name="photography[type]" class="form-control" placeholder="Photo Type (Candid / Traditional)">
    </div>

    <div class="col-md-6 mb-3">
        <input name="photography[duration]" class="form-control" placeholder="Duration (hours)">
    </div>

    <div class="col-md-6 mb-3">
        <input name="photography[videography]" class="form-control" placeholder="Video Required? (Yes/No)">
    </div>

<?php } ?>


<?php if($vendor['vendor_type']=="beauty"){ ?>

    <div class="col-md-6 mb-3">
        <input name="beauty[service]" class="form-control" placeholder="Service Type (Bridal / Makeup)">
    </div>

    <div class="col-md-6 mb-3">
        <input type="number" name="beauty_person" class="form-control" placeholder="Number of Persons">
    </div>

    <div class="col-md-6 mb-3">
        <input name="beauty[package]" class="form-control" placeholder="Package Type (Basic/Premium)">
    </div>

<?php } ?>


<?php if($vendor['vendor_type']=="card_vendor"){ ?>

    <div class="col-md-6 mb-3">
        <input name="cards[type]" class="form-control" placeholder="Card Type (Digital/Printed)">
    </div>

    <div class="col-md-6 mb-3">
        <input type="number"
               name="card_count"
               class="form-control"
               placeholder="Number of Cards Required"
               min="0"
               required>
    </div>

<?php } ?>

</div>

<!-- PREVIEW BUTTON -->
<button type="submit" class="btn btn-submit w-100 mt-3">
Preview Booking
</button>

</form>

</div>

</div>
</div>
</div>

</body>
</html>