
<?php 
session_start();
$conn = new mysqli("localhost","root","","event_management_system");

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$user = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM users WHERE id='$user_id'"
));

/* ================= CHECK IF CATERER EXISTS IN CART ================= */

$has_caterer = false;

$check_cart = mysqli_query($conn,"
SELECT vendors.vendor_type
FROM cart
JOIN vendors ON cart.vendor_id = vendors.vendor_id
WHERE cart.user_id='$user_id'
");

while($row = mysqli_fetch_assoc($check_cart)){

    if(strtolower($row['vendor_type']) == 'caterer'){
        $has_caterer = true;
        break;
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Book Event - Step 1</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f7fb;
    font-family:Poppins;
}

.card-box{
    background:#ffffff;
    border-radius:18px;
    padding:30px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

.heading{
    font-weight:700;
    margin-bottom:25px;
    color:#2d3748;
}

.section-title{
    font-weight:600;
    margin:18px 0 12px;
    color:#444;
    font-size:18px;
}

.form-control{
    border-radius:12px;
    padding:12px;
    border:1px solid #dbe3ea;
}

.form-control:focus{
    box-shadow:none;
    border-color:#7c9cff;
}

.btn-next{
    border-radius:14px;
    padding:14px;
    font-weight:600;
    font-size:16px;
    background:#6c8cff;
    border:none;
}

.btn-next:hover{
    background:#5878f0;
}

.info-box{
    background:#eef4ff;
    padding:12px 15px;
    border-radius:12px;
    color:#3554b4;
    margin-bottom:15px;
    font-size:14px;
}

</style>

</head>

<body>

<div class="container mt-5 mb-5">

<div class="card-box">

<h3 class="heading text-center">📅 Book Your Event</h3>

<form method="POST" action="book_all_services.php">

<!-- ================= CUSTOMER INFO ================= -->

<div class="section-title">
👤 Customer Information
</div>

<input type="text"
name="customer_name"
value="<?php echo $user['name']; ?>"
class="form-control mb-3"
placeholder="Enter your full name"
required>

<input type="email"
name="customer_email"
value="<?php echo $user['email']; ?>"
class="form-control mb-3"
placeholder="Enter your email address"
required>

<input type="text"
name="customer_phone"
value="<?php echo $user['mobile']; ?>"
class="form-control mb-3"
placeholder="Enter your mobile number"
required>

<hr>

<!-- ================= EVENT DETAILS ================= -->

<div class="section-title">
🎉 Event Details
</div>

<input type="date"
name="event_date"
class="form-control mb-3"
required>

<input type="text"
name="event_time"
class="form-control mb-3"
placeholder="Example: 6:00 PM"
required>

<input type="text"
name="event_location"
class="form-control mb-3"
placeholder="Enter event location (Hall / Address)"
required>

<input type="number"
name="guest_count"
class="form-control mb-3"
placeholder="Total Guests"
required>

<!-- ================= SHOW ONLY IF CATERER EXISTS ================= -->

<!-- 


<div class="info-box">
🍽 Catering service detected in your cart.  
Please enter Veg and Non-Veg guest counts.
</div>

<div class="row">

    <div class="col-md-6">

        <input type="number"
        name="veg_count"
        class="form-control mb-3"
        placeholder="Number of Veg plates"
        min="0">

    </div>

    <div class="col-md-6">

        <input type="number"
        name="nonveg_count"
        class="form-control mb-3"
        placeholder="Number of Non-Veg plates"
        min="0">

    </div>

</div>



<textarea name="common_request"
class="form-control mb-4"
rows="4"
placeholder="Any special instructions for all vendors (optional)"></textarea> -->

<!-- ================= BUTTON ================= -->

<button class="btn btn-next w-100">
Next → Choose Services
</button>

</form>

</div>

</div>

</body>
</html>