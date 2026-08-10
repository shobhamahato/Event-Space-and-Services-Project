<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../../config/db.php");

if(isset($_POST['submit'])){

    // ===== BASIC INFO =====
    $venue_name = $_POST['venue_name'];
    $owner_name = $_POST['owner_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // ===== ADDRESS =====
    $street = $_POST['street'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];

    // ===== VENUE DETAILS =====
    $venue_type = $_POST['venue_type'];
    $min_capacity = $_POST['min_capacity'];
    $max_capacity = $_POST['max_capacity'];
    $price_per_plate = $_POST['price_per_plate'];
    $rental_price = $_POST['rental_price'];
    $ac_available = $_POST['ac_available'];
    $parking_capacity = $_POST['parking_capacity'];

    $facilities = isset($_POST['facilities']) ? implode(",", $_POST['facilities']) : "";

    // ===== POLICIES =====
    $outside_catering = $_POST['outside_catering'];
    $outside_decoration = $_POST['outside_decoration'];
    $advance_payment = $_POST['advance_payment'];
    $cancellation_policy = $_POST['cancellation_policy'];

    $about_venue = $_POST['about_venue'];

    // ==========================
    // INSERT INTO vendors TABLE
    // ==========================
    $vendor_type = "venue";

    $vendor_stmt = $conn->prepare("INSERT INTO vendors 
        (vendor_type, business_name, owner_name, email, phone, password) 
        VALUES (?, ?, ?, ?, ?, ?)");

    $vendor_stmt->bind_param("ssssss",
        $vendor_type,
        $venue_name,
        $owner_name,
        $email,
        $phone,
        $password
    );

    if($vendor_stmt->execute()){

        $vendor_id = $vendor_stmt->insert_id;

        // ==========================
        // IMAGE UPLOAD
        // ==========================
        $uploaded_images = [];

        if(!empty($_FILES['venue_images']['name'][0])){

            foreach($_FILES['venue_images']['name'] as $key => $image_name){

                $tmp_name = $_FILES['venue_images']['tmp_name'][$key];
                $new_name = time() . "_" . basename($image_name);
                $upload_dir = "../../uploads/venues/";

                if(!is_dir($upload_dir)){
                    mkdir($upload_dir, 0777, true);
                }

                $upload_path = $upload_dir . $new_name;
                move_uploaded_file($tmp_name, $upload_path);
                $uploaded_images[] = $new_name;
            }
        }

        $venue_images = implode(",", $uploaded_images);

        // ==========================
        // INSERT INTO venues TABLE
        // ==========================
        $venue_stmt = $conn->prepare("INSERT INTO venues
            (vendor_id, street, city, pincode, venue_type, min_capacity, max_capacity, price_per_plate, rental_price, ac_available, parking_capacity, facilities, outside_catering, outside_decoration, advance_payment, cancellation_policy, venue_images, about_venue)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

      $venue_stmt->bind_param(
            "issssiiddsisssisss",
            $vendor_id,
            $street,
            $city,
            $pincode,
            $venue_type,
            $min_capacity,
            $max_capacity,
            $price_per_plate,
            $rental_price,
            $ac_available,
            $parking_capacity,
            $facilities,
            $outside_catering,
            $outside_decoration,
            $advance_payment,
            $cancellation_policy,
            $venue_images,
            $about_venue
            );

        if($venue_stmt->execute()){
            echo "<script>
                    alert('Registration Successful! Waiting for Admin Approval');
                    window.location='../login.php';
                  </script>";
        } else {
            echo "Venue Error: " . $venue_stmt->error;
        }

        $venue_stmt->close();

    } else {
        echo "Vendor Error: " . $vendor_stmt->error;
    }

    $vendor_stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Venue Registration | EventSpace</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
body {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    font-family: 'Montserrat', sans-serif;
    margin: 0;
    padding: 0;
}

.page-wrapper {
    margin-top: 120px;
    margin-bottom: 60px;
    display: flex;
    justify-content: center;
}

.register-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 45px;
    width: 100%;
    max-width: 900px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
}

.register-title {
    text-align: center;
    font-weight: 700;
    margin-bottom: 35px;
    color: #203a43;
    font-size: 28px;
}

.section-title {
    font-weight: 600;
    margin-top: 30px;
    margin-bottom: 15px;
    color: #2c5364;
    border-left: 4px solid #1cc88a;
    padding-left: 10px;
}

.form-control, .form-select {
    border-radius: 10px;
    padding: 10px;
}

textarea {
    resize: none;
}

.btn-register {
    background-color: #1cc88a;
    color: white;
    border-radius: 30px;
    padding: 12px;
    font-weight: 600;
    font-size: 16px;
}

.btn-register:hover {
    background-color: #17a673;
}

.small-text {
    font-size: 13px;
}
</style>
</head>
<body>

<div class="page-wrapper">
<div class="register-card">

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="../../index.php" class="btn btn-light shadow-sm rounded-pill px-4">
        <i class="bi bi-arrow-left"></i> Back to Home
    </a>
</div>

<h3 class="register-title">Venue Vendor Registration</h3>

<form method="POST" enctype="multipart/form-data" id="regForm" onsubmit="validate(event)">

<!-- BASIC INFO -->
<div class="section-title">Basic Information</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Venue Name</label>
        <input type="text" name="venue_name" class="form-control">
        <label id="venueNameError" class="text-danger"></label>
    </div>
    <div class="col-md-6 mb-3">
        <label>Owner Name</label>
        <input type="text" name="owner_name" class="form-control">
        <label id="ownerNameError" class="text-danger"></label>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control">
        <label id="emailError" class="text-danger"></label>
    </div>
    <div class="col-md-6 mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control">
        <label id="phoneError" class="text-danger"></label>
    </div>
</div>

<!-- ADDRESS -->
<div class="section-title">Venue Address</div>

<div class="mb-3">
    <label>Street</label>
    <input type="text" name="street" class="form-control">
    <label id="streetError" class="text-danger"></label>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>City</label>
        <input type="text" name="city" class="form-control">
        <label id="cityError" class="text-danger"></label>
    </div>
    <div class="col-md-6 mb-3">
        <label>Pincode</label>
        <input type="text" name="pincode" class="form-control">
        <label id="pincodeError" class="text-danger"></label>
    </div>
</div>

<!-- VENUE DETAILS -->
<div class="section-title">Venue Details</div>

<div class="mb-3">
    <label>Venue Type</label>
    <select name="venue_type" class="form-select">
        <option value="">Select Type</option>
        <option>Banquet Hall</option>
        <option>Marriage Garden</option>
        <option>Farmhouse</option>
        <option>Hotel</option>
        <option>Resort</option>
        <option>Community Hall</option>
    </select>
    <label id="venueTypeError" class="text-danger"></label>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Minimum Capacity</label>
        <input type="number" name="min_capacity" class="form-control">
        <label id="minCapacityError" class="text-danger"></label>
    </div>
    <div class="col-md-6 mb-3">
        <label>Maximum Capacity</label>
        <input type="number" name="max_capacity" class="form-control">
        <label id="maxCapacityError" class="text-danger"></label>
    </div>
</div>

<div class="mb-3">
    <label>Parking Capacity</label>
    <input type="number" name="parking_capacity" class="form-control">
    <label id="parkingError" class="text-danger"></label>
</div>

<div class="mb-3">
    <label>AC Available?</label>
    <select name="ac_available" class="form-select">
        <option value="">Select</option>
        <option>Yes</option>
        <option>No</option>
    </select>
    <label id="acError" class="text-danger"></label>
</div>
<!-- FACILITIES -->
<div class="section-title">Facilities Available</div>

<div class="row">

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="facilities[]" value="Power Backup">
<label class="form-check-label">Power Backup</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="facilities[]" value="Guest Rooms">
<label class="form-check-label">Guest Rooms</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="facilities[]" value="Outdoor Lawn">
<label class="form-check-label">Outdoor Lawn</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="facilities[]" value="DJ Setup">
<label class="form-check-label">DJ Setup</label>
</div>
</div>

</div>
<!-- POLICIES -->
<div class="section-title">Venue Policies</div>

<div class="mb-3">
    <label>Outside Catering</label>
    <select name="outside_catering" class="form-select">
        <option value="">Select</option>
        <option>Yes</option>
        <option>No</option>
    </select>
    <label id="cateringError" class="text-danger"></label>
</div>

<div class="mb-3">
    <label>Outside Decoration</label>
    <select name="outside_decoration" class="form-select">
        <option value="">Select</option>
        <option>Yes</option>
        <option>No</option>
    </select>
    <label id="decorationError" class="text-danger"></label>
</div>

<div class="mb-3">
    <label>Advance Payment (%)</label>
    <input type="number" name="advance_payment" class="form-control">
    <label id="advanceError" class="text-danger"></label>
</div>

<div class="mb-3">
    <label>Cancellation Policy</label>
    <textarea name="cancellation_policy" class="form-control"></textarea>
    <label id="cancelError" class="text-danger"></label>
</div>

<div class="mb-3">
    <label>Upload Images</label>
    <input type="file" name="venue_images[]" class="form-control" multiple>
    <label id="imageError" class="text-danger"></label>
</div>

<div class="mb-3">
    <textarea name="about_venue" class="form-control" placeholder="About Venue"></textarea>
    <label id="aboutError" class="text-danger"></label>
</div>

<div class="mb-3">
    <input type="password" name="password" class="form-control" placeholder="Password">
    <label id="passwordError" class="text-danger"></label>
</div>

<button type="submit" class="btn btn-register w-100 mt-3" name="submit">
Submit for Approval
</button>

</form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function validate(e){

let error = false;
let form = document.getElementById("regForm");

// values
let venue = form.venue_name.value;
let owner = form.owner_name.value;
let email = form.email.value;
let phone = form.phone.value;
let street = form.street.value;
let city = form.city.value;
let pincode = form.pincode.value;
let venue_type = form.venue_type.value;
let min = form.min_capacity.value;
let max = form.max_capacity.value;
let parking = form.parking_capacity.value;
let ac = form.ac_available.value;
let catering = form.outside_catering.value;
let decoration = form.outside_decoration.value;
let advance = form.advance_payment.value;
let cancel = form.cancellation_policy.value;
let images = form["venue_images[]"].files;
let about = form.about_venue.value;
let password = form.password.value;

// patterns
let namePattern = /^[A-Za-z ]+$/;
let phonePattern = /^[6-9][0-9]{9}$/;
let pincodePattern = /^[0-9]{6}$/;
let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

// CLEAR ALL
document.querySelectorAll(".text-danger").forEach(el => el.innerHTML = "");

// VALIDATIONS

if(venue=="" || !namePattern.test(venue)){
document.getElementById("venueNameError").innerHTML="Enter valid venue name";
error=true;
}

if(owner=="" || !namePattern.test(owner)){
document.getElementById("ownerNameError").innerHTML="Enter valid owner name";
error=true;
}

if(!emailPattern.test(email)){
document.getElementById("emailError").innerHTML="Enter valid email";
error=true;
}

if(!phonePattern.test(phone)){
document.getElementById("phoneError").innerHTML="Enter valid phone";
error=true;
}

if(street==""){
document.getElementById("streetError").innerHTML="Street required";
error=true;
}

if(city=="" || !namePattern.test(city)){
document.getElementById("cityError").innerHTML="Enter valid city";
error=true;
}

if(!pincodePattern.test(pincode)){
document.getElementById("pincodeError").innerHTML="Invalid pincode";
error=true;
}

if(venue_type==""){
document.getElementById("venueTypeError").innerHTML="Select venue type";
error=true;
}

if(min<=0){
document.getElementById("minCapacityError").innerHTML="Enter valid min capacity";
error=true;
}

if(max<=min){
document.getElementById("maxCapacityError").innerHTML="Max > Min required";
error=true;
}

if(parking<0){
document.getElementById("parkingError").innerHTML="Invalid parking";
error=true;
}

if(ac==""){
document.getElementById("acError").innerHTML="Select option";
error=true;
}

if(catering==""){
document.getElementById("cateringError").innerHTML="Select option";
error=true;
}

if(decoration==""){
document.getElementById("decorationError").innerHTML="Select option";
error=true;
}

if(advance<0 || advance>100){
document.getElementById("advanceError").innerHTML="0-100 only";
error=true;
}

if(cancel==""){
document.getElementById("cancelError").innerHTML="Required";
error=true;
}

if(images.length==0){
document.getElementById("imageError").innerHTML="Upload images";
error=true;
}

if(about==""){
document.getElementById("aboutError").innerHTML="Required";
error=true;
}

// PASSWORD
let passMsg="";
if(password.length<8) passMsg+="Min 8 chars<br>";
if(!/[A-Z]/.test(password)) passMsg+="1 uppercase<br>";
if(!/[a-z]/.test(password)) passMsg+="1 lowercase<br>";
if(!/[0-9]/.test(password)) passMsg+="1 number<br>";
if(!/[@#$%^&]/.test(password)) passMsg+="1 special char<br>";

if(passMsg!=""){
document.getElementById("passwordError").innerHTML=passMsg;
error=true;
}

if(error){
e.preventDefault();
}
}
</script>

</body>
</html>