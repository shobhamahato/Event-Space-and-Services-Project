<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../../config/db.php");

if(isset($_POST['submit'])){

// ===== BASIC DETAILS =====
$business_name = $_POST['business_name'];
$owner_name = $_POST['owner_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];

// ===== ADDRESS =====
$street = $_POST['street'];
$city = $_POST['city'];
$pincode = $_POST['pincode'];

// ===== SERVICES =====
$services = isset($_POST['services']) ? implode(",", $_POST['services']) : "";
$event_types = $_POST['event_types'];
$price_per_event = $_POST['price_per_event'];
$price_per_hour = $_POST['price_per_hour'];
$experience = $_POST['experience'];
$travel_available = $_POST['travel_available'];
$packages = $_POST['packages'];
$equipment_details = $_POST['equipment_details'];
$about = $_POST['about'];



// INSERT INTO vendors TABLE

$vendor_type = "music_dj";

$vendor_stmt = $conn->prepare("INSERT INTO vendors
(vendor_type,business_name,owner_name,email,phone,password)
VALUES (?,?,?,?,?,?)");

$vendor_stmt->bind_param("ssssss",
$vendor_type,
$business_name,
$owner_name,
$email,
$phone,
$password
);

if($vendor_stmt->execute()){

$vendor_id = $conn->insert_id;


// IMAGE UPLOAD

$uploaded_files = [];

if(!empty($_FILES['portfolio']['name'][0])){

$upload_dir = "../../uploads/portfolio/";

if(!is_dir($upload_dir)){
mkdir($upload_dir,0777,true);
}

foreach($_FILES['portfolio']['name'] as $key => $file_name){

$tmp_name = $_FILES['portfolio']['tmp_name'][$key];

$new_name = time()."_".basename($file_name);

$upload_path = $upload_dir.$new_name;

if(move_uploaded_file($tmp_name,$upload_path)){
$uploaded_files[] = $new_name;
}

}

}

// INSERT INTO vendor_portfolio

if(!empty($uploaded_files)){

$portfolio_stmt = $conn->prepare("INSERT INTO vendor_portfolio (vendor_id,image_path) VALUES (?,?)");

foreach($uploaded_files as $img){

$portfolio_stmt->bind_param("is",$vendor_id,$img);
$portfolio_stmt->execute();

}

$portfolio_stmt->close();

}

// INSERT INTO music_vendors TABLE

$portfolio_files = implode(",", $uploaded_files);

$music_stmt = $conn->prepare("INSERT INTO music_vendors
(vendor_id,street,city,pincode,services,event_types,price_per_event,price_per_hour,experience,travel_available,packages,equipment_details,portfolio_files,about)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

$music_stmt->bind_param("isssssddisssss",
$vendor_id,
$street,
$city,
$pincode,
$services,
$event_types,
$price_per_event,
$price_per_hour,
$experience,
$travel_available,
$packages,
$equipment_details,
$portfolio_files,
$about
);

if($music_stmt->execute()){

echo "<script>
alert('Registration Successful! Waiting for Admin Approval');
window.location='../login.php';
</script>";

}else{

echo "Music Vendor Error: ".$music_stmt->error;

}

$music_stmt->close();

}else{

echo "Vendor Error: ".$vendor_stmt->error;

}

$vendor_stmt->close();
$conn->close();

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Music & DJ Registration | EventSpace</title>
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

<h3 class="register-title">Music & DJ Vendor Registration</h3>

<form method="POST" enctype="multipart/form-data" id="regForm" onsubmit="validate(event)">

<!-- BASIC INFO -->
<div class="section-title">Basic Information</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Business / Stage Name</label>
        <input type="text" name="business_name" class="form-control" required>
        <label id="businessNameError" class="text-danger"style="color:red;"></label>
    </div>
    <div class="col-md-6 mb-3">
        <label>Owner / DJ Name</label>
        <input type="text" name="owner_name" class="form-control" required>
        <label id="ownerNameError" class="text-danger"style="color:red;"></label>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
        <label id="emailError" class="text-danger" style="color:red;"></label>
    </div>
    <div class="col-md-6 mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" required>
        <label id="phoneError" class="text-danger"style="color:red;"></label>
    </div>
</div>

<!-- ADDRESS -->
<div class="section-title">Business Address</div>

<div class="mb-3">
    <label>Street</label>
    <input type="text" name="street" class="form-control" required>
    <label id="streetError" class="text-danger"style="color:red;"></label>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>City</label>
        <input type="text" name="city" class="form-control" required>
        <label id="cityError" class="text-danger"style="color:red;"></label>
    </div>
    <div class="col-md-6 mb-3">
        <label>Pincode</label>
        <input type="text" name="pincode" class="form-control" required>
        <label id="pincodeError" class="text-danger"style="color:red;"></label>
    </div>
</div>

<!-- SERVICES -->
<div class="section-title">Services Offered</div>

<div class="row">

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="DJ" >
<label class="form-check-label">DJ</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="Live Band">
<label class="form-check-label">Live Band</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="Singer">
<label class="form-check-label">Singer</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="Sound System Rental">
<label class="form-check-label">Sound System Rental</label>
</div>
</div>
</div>

<!-- EVENT TYPES -->
<div class="section-title">Events Covered</div>

<div class="mb-3">
<input type="text" name="event_types" class="form-control" placeholder="Wedding, Birthday, Corporate, Reception etc." required>
</div>

<!-- PRICING -->
<div class="section-title">Pricing Details</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Price Per Event (₹)</label>
        <input type="number" name="price_per_event" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label>Price Per Hour (₹)</label>
        <input type="number" name="price_per_hour" class="form-control">
    </div>
</div>

<div class="mb-3">
    <label>Experience (Years)</label>
    <input type="number" name="experience" class="form-control" required>
    <label id="experienceError" class="text-danger" style="color:red;"></label>
</div>

<div class="mb-3">
    <label>Available for Outstation Events?</label>
    <select name="travel_available" class="form-select" required>
        <option value="">Select</option>
        <option>Yes</option>
        <option>No</option>
    </select>
</div>

<div class="mb-3">
    <label>Package Details</label>
    <textarea name="packages" class="form-control" rows="3" placeholder="Basic, Premium, Luxury packages with details" required></textarea>
</div>

<!-- EQUIPMENT -->
<div class="section-title">Sound & Equipment Details</div>

<div class="mb-3">
    <textarea name="equipment_details" class="form-control" rows="3" placeholder="Speakers, Subwoofers, Lights, Smoke Machine etc." required></textarea>
</div>

<!-- PORTFOLIO -->
<div class="section-title">Portfolio</div>

<!-- <div class="mb-3">
    <label>Upload Event Images / Videos</label>
    <input type="file" name="portfolio[]" class="form-control" multiple required>
    <label id="portfolioError" class="text-danger"></label>
</div> -->

<!-- ABOUT -->
<div class="section-title">About Your Services</div>

<div class="mb-3">
    <textarea name="about" class="form-control" rows="4" required></textarea>
    <label id="aboutError" class="text-danger"></label>
</div>

<!-- PASSWORD -->
<div class="section-title">Account Security</div>

<div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
    <label id="passwordError" class="text-danger"style="color:red;"></label>
</div>

<button type="submit" class="btn btn-register w-100 mt-3" name="submit">
    Submit for Approval
</button>

<div class="text-center mt-3 small-text">
    After submission, admin will review and approve your account.
</div>

</form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function validate(e){
    let error = false;

    let form = document.getElementById("regForm");

    let business = form.elements['business_name'].value;
    let owner = form.elements['owner_name'].value;
    let email = form.elements['email'].value;
    let phone = form.elements['phone'].value;
    let street = form.elements['street'].value;
    let city = form.elements['city'].value;
    let pincode = form.elements['pincode'].value;
    let experience = form.elements['experience'].value;
    // let portfolio = form.elements['portfolio'].files;
    let about = form.elements['about'].value;
    let password = form.elements['password'].value;

    // ERROR ELEMENTS
    let businessNameError = document.getElementById("businessNameError");
    let ownerNameError = document.getElementById("ownerNameError");
    let emailError = document.getElementById("emailError");
    let phoneError = document.getElementById("phoneError");
    let streetError = document.getElementById("streetError");
    let cityError = document.getElementById("cityError");
    let pincodeError = document.getElementById("pincodeError");
    let experienceError = document.getElementById("experienceError");
    // let portfolioError = document.getElementById("portfolioError");
    let aboutError = document.getElementById("aboutError");
    let passwordError = document.getElementById("passwordError");

    // PATTERNS
    let namePattern = /^[A-Za-z ]+$/;
    let phonePattern = /^[6-9][0-9]{9}$/;
    let pincodePattern = /^[0-9]{6}$/;
    let emailPattern = /^[a-z0-9_\.]{3,}@[a-z0-9\.]{3,15}\.[a-z]{2,5}$/;

    businessNameError.innerHTML = "";
    ownerNameError.innerHTML = "";
    emailError.innerHTML = "";
    phoneError.innerHTML = "";
    streetError.innerHTML = "";
    cityError.innerHTML = "";
    pincodeError.innerHTML = "";
    experienceError.innerHTML = "";
    aboutError.innerHTML = "";
    passwordError.innerHTML = "";
    // BUSINESS NAME
    if(business === "" || !namePattern.test(business)){
        businessNameError.innerHTML = "Enter valid business name";
        error = true;
    } else {
        businessNameError.innerHTML = "";
    }

    // OWNER
    if(owner === "" || !namePattern.test(owner)){
        ownerNameError.innerHTML = "Enter valid owner name";
        error = true;
    } else {
        ownerNameError.innerHTML = "";
    }

    // EMAIL
    if(email === ""){
        emailError.innerHTML = "Email is required";
        error = true;
    } else if(!emailPattern.test(email)){
        emailError.innerHTML = "Enter valid email";
        error = true;
    } else {
        emailError.innerHTML = "";
    }

    // PHONE
    if(phone === ""){
        phoneError.innerHTML = "Phone is required";
        error = true;
    } else if(!phonePattern.test(phone)){
        phoneError.innerHTML = "Enter valid 10 digit number";
        error = true;
    } else {
        phoneError.innerHTML = "";
    }

    // STREET
    if(street === ""){
        streetError.innerHTML = "Street is required";
        error = true;
    } else {
        streetError.innerHTML = "";
    }

    // CITY
    if(city === "" || !namePattern.test(city)){
        cityError.innerHTML = "Enter valid city";
        error = true;
    } else {
        cityError.innerHTML = "";
    }

    // PINCODE
    if(!pincodePattern.test(pincode)){
        pincodeError.innerHTML = "Enter valid 6 digit pincode";
        error = true;
    } else {
        pincodeError.innerHTML = "";
    }

    // EXPERIENCE
    if(experience === "" || experience < 0){
        experienceError.innerHTML = "Enter valid experience";
        error = true;
    } else {
        experienceError.innerHTML = "";
    }

    // PORTFOLIO (ONLY 1 IMAGE)
    // if(portfolio.length === 0){
    //     portfolioError.innerHTML = "Upload image";
    //     error = true;
    // } else {
    //     portfolioError.innerHTML = "";
    // }

    // ABOUT
    if(about === ""){
        aboutError.innerHTML = "About is required";
        error = true;
    } else {
        aboutError.innerHTML = "";
    }

    // PASSWORD
    let passErrMsg = "";

    if(password === ""){
        passErrMsg += "Password is required<br>";
        error = true;
    }
    if(!/[a-z]/.test(password)){
        passErrMsg += "1 lowercase required<br>";
        error = true;
    }
    if(!/[A-Z]/.test(password)){
        passErrMsg += "1 uppercase required<br>";
        error = true;
    }
    if(!/[0-9]/.test(password)){
        passErrMsg += "1 number required<br>";
        error = true;
    }
    if(!/[@#$%^&]/.test(password)){
        passErrMsg += "1 special character required<br>";
        error = true;
    }
    if(password.length < 8 || password.length > 15){
        passErrMsg += "8–15 characters required<br>";
        error = true;
    }

    passwordError.innerHTML = passErrMsg;

    // FINAL CHECK
    if(error){
        e.preventDefault();
    }
}
</script>
</body>
</html>