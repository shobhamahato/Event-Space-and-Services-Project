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
    $bridal_price = $_POST['bridal_price'];
    $packages = $_POST['packages'];
    $experience = $_POST['experience'];
    $home_service = $_POST['home_service'];
    $products_used = $_POST['products_used'];
    $about = $_POST['about'];

    // INSERT INTO vendors TABLE
    // ==========================
    $vendor_type = "beauty_parlour";

    $vendor_stmt = $conn->prepare("INSERT INTO vendors 
        (vendor_type, business_name, owner_name, email, phone, password) 
        VALUES (?, ?, ?, ?, ?, ?)");

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

        // ==========================
        // IMAGE UPLOAD (ONLY 1 IMAGE)
        // ==========================
        $uploaded_image = "";

        if(!empty($_FILES['portfolio']['name'])){

            $upload_dir = "../../uploads/portfolio/";

            if(!is_dir($upload_dir)){
                mkdir($upload_dir,0777,true);
            }

            $image_name = $_FILES['portfolio']['name'];
            $tmp_name = $_FILES['portfolio']['tmp_name'];

            $new_name = time()."_".basename($image_name);
            $upload_path = $upload_dir.$new_name;

            if(move_uploaded_file($tmp_name,$upload_path)){
                $uploaded_image = $new_name;
            }
        }

        // ==========================
        // INSERT INTO vendor_portfolio
        // ==========================
        if(!empty($uploaded_image)){
            $portfolio_stmt = $conn->prepare("INSERT INTO vendor_portfolio (vendor_id,image_path) VALUES (?,?)");
            $portfolio_stmt->bind_param("is",$vendor_id,$uploaded_image);
            $portfolio_stmt->execute();
            $portfolio_stmt->close();
        }

        // ==========================
        // INSERT INTO beauty_parlours TABLE
        // ==========================
        $portfolio_images = $uploaded_image;

        $parlour_stmt = $conn->prepare("INSERT INTO beauty_parlours
            (vendor_id,street,city,pincode,services,bridal_price,packages,experience,home_service,products_used,portfolio_images,about)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

        $parlour_stmt->bind_param("issssdsissss",
            $vendor_id,
            $street,
            $city,
            $pincode,
            $services,
            $bridal_price,
            $packages,
            $experience,
            $home_service,
            $products_used,
            $portfolio_images,
            $about
        );

        if($parlour_stmt->execute()){
            echo "<script>
                    alert('Registration Successful! Waiting for Admin Approval');
                    window.location='../login.php';
                  </script>";
        } else {
            echo "Beauty Parlour Error: " . $parlour_stmt->error;
        }

        $parlour_stmt->close();

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
<title>Beauty Parlour Registration | EventSpace</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>

body{
background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
font-family:'Montserrat',sans-serif;
}

.page-wrapper{
margin-top:120px;
margin-bottom:60px;
display:flex;
justify-content:center;
}

.register-card{
background:#fff;
border-radius:20px;
padding:45px;
width:100%;
max-width:900px;
box-shadow:0 25px 50px rgba(0,0,0,0.25);
}

.register-title{
text-align:center;
font-weight:700;
margin-bottom:35px;
color:#203a43;
font-size:28px;
}

.section-title{
font-weight:600;
margin-top:30px;
margin-bottom:15px;
color:#2c5364;
border-left:4px solid #ff69b4;
padding-left:10px;
}

.form-control{
border-radius:10px;
padding:10px;
}

textarea{
resize:none;
}

.btn-register{
background-color:#ff69b4;
color:white;
border-radius:30px;
padding:12px;
font-weight:600;
font-size:16px;
}

.btn-register:hover{
background-color:#e055a1;
}

.small-text{
font-size:13px;
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

<h3 class="register-title">Beauty Registration</h3>

<form method="POST" enctype="multipart/form-data" id="regForm" onsubmit="validate(event)">

<!-- BASIC INFO -->
<div class="section-title">Basic Information</div>

<input type="text" name="business_name" class="form-control mb-2" placeholder="Business Name">
<label id="businessNameError" class="text-danger"></label>

<input type="text" name="owner_name" class="form-control mb-2 mt-2" placeholder="Owner Name">
<label id="ownerNameError" class="text-danger"></label>

<input type="email" name="email" class="form-control mb-2 mt-2" placeholder="Email">
<label id="emailError" class="text-danger"></label>

<input type="text" name="phone" class="form-control mb-2 mt-2" placeholder="Phone">
<label id="phoneError" class="text-danger"></label>


<!-- ADDRESS -->
<div class="section-title">Address</div>

<input type="text" name="street" class="form-control mb-2" placeholder="Street">
<label id="streetError" class="text-danger"></label>

<input type="text" name="city" class="form-control mb-2 mt-2" placeholder="City">
<label id="cityError" class="text-danger"></label>

<input type="text" name="pincode" class="form-control mb-2 mt-2" placeholder="Pincode">
<label id="pincodeError" class="text-danger"></label>


<!-- SERVICES -->
<div class="section-title">Beauty Services</div>

<input type="number" name="bridal_price" class="form-control mb-2" placeholder="Bridal Price">
<label id="bridalPriceError" class="text-danger"></label>

<textarea name="packages" class="form-control mb-3 mt-2" placeholder="Packages"></textarea>

<input type="number" name="experience" class="form-control mb-2" placeholder="Experience (Years)">
<label id="experienceError" class="text-danger"></label>

<select name="home_service" class="form-control mb-2 mt-2">
<option value="">Home Service</option>
<option>Yes</option>
<option>No</option>
</select>
<label id="homeServiceError" class="text-danger"></label>

<input type="text" name="products_used" class="form-control mb-3 mt-2" placeholder="Products Used">


<!-- PORTFOLIO -->
<div class="section-title">Portfolio</div>

<input type="file" name="portfolio" class="form-control mb-2">
<label id="portfolioError" class="text-danger"></label>


<!-- ABOUT -->
<div class="section-title">About</div>

<textarea name="about" class="form-control mb-2" placeholder="About Your Business"></textarea>
<label id="aboutError" class="text-danger"></label>


<!-- PASSWORD -->
<div class="section-title">Security</div>

<input type="password" name="password" class="form-control mb-2" placeholder="Password">
<label id="passwordError" class="text-danger"></label>


<button type="submit" name="submit" class="btn btn-register w-100 mt-3">
Submit for Approval
</button>

<div class="text-center mt-3 small-text">
After submission, admin will review and approve your account.
</div>

</form>

</div>
</div>

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
    let bridal_price = form.elements['bridal_price'].value;
    let experience = form.elements['experience'].value;
    let home_service = form.elements['home_service'].value;
    let portfolio = form.elements['portfolio'].files;
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
    let bridalPriceError = document.getElementById("bridalPriceError");
    let experienceError = document.getElementById("experienceError");
    let homeServiceError = document.getElementById("homeServiceError");
    let portfolioError = document.getElementById("portfolioError");
    let aboutError = document.getElementById("aboutError");
    let passwordError = document.getElementById("passwordError");

    // PATTERNS
    let namePattern = /^[A-Za-z ]+$/;
    let phonePattern = /^[6-9][0-9]{9}$/;
    let pincodePattern = /^[0-9]{6}$/;
    let emailPattern = /^[a-z0-9_\.]{3,}@[a-z0-9\.]{3,15}\.[a-z]{2,5}$/;

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

    // BRIDAL PRICE
    if(bridal_price === "" || bridal_price < 0){
        bridalPriceError.innerHTML = "Enter valid price";
        error = true;
    } else {
        bridalPriceError.innerHTML = "";
    }

    // EXPERIENCE
    if(experience === "" || experience < 0){
        experienceError.innerHTML = "Enter valid experience";
        error = true;
    } else {
        experienceError.innerHTML = "";
    }

    // HOME SERVICE
    if(home_service === ""){
        homeServiceError.innerHTML = "Select option";
        error = true;
    } else {
        homeServiceError.innerHTML = "";
    }

    // PORTFOLIO (ONLY 1 IMAGE)
    if(portfolio.length === 0){
        portfolioError.innerHTML = "Upload image";
        error = true;
    } else {
        portfolioError.innerHTML = "";
    }

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