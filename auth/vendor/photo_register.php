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

    // ===== PHOTOGRAPHY DETAILS =====
    $services = isset($_POST['services']) ? implode(",", $_POST['services']) : "";
    $starting_price = $_POST['starting_price'];
    $equipment_type = $_POST['equipment_type'];
    $packages = $_POST['packages'];
    $experience = $_POST['experience'];
    $about_business = $_POST['about_business'];

    // ==========================
    // INSERT INTO vendors TABLE
    // ==========================
    $vendor_type = "photography";

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

        $vendor_id = $vendor_stmt->insert_id;

        // ==========================
        // IMAGE UPLOAD
        // ==========================
        $uploaded_images = [];

        if(!empty($_FILES['portfolio_images']['name'][0])){

            foreach($_FILES['portfolio_images']['name'] as $key => $image_name){

                $tmp_name = $_FILES['portfolio_images']['tmp_name'][$key];
                $new_name = time() . "_" . basename($image_name);
                $upload_dir = "../../uploads/photography/";

                if(!is_dir($upload_dir)){
                    mkdir($upload_dir, 0777, true);
                }

                $upload_path = $upload_dir . $new_name;
                move_uploaded_file($tmp_name, $upload_path);
                $uploaded_images[] = $new_name;
            }
        }

        $portfolio_images = implode(",", $uploaded_images);

        // ==========================
        // INSERT INTO photography_vendors TABLE
        // ==========================
        $photo_stmt = $conn->prepare("INSERT INTO photography_vendors
            (vendor_id, street, city, pincode, services, starting_price, equipment_type, packages, experience, portfolio_images, about_business)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $photo_stmt->bind_param("issssdssiss",
            $vendor_id,
            $street,
            $city,
            $pincode,
            $services,
            $starting_price,
            $equipment_type,
            $packages,
            $experience,
            $portfolio_images,
            $about_business
        );

        if($photo_stmt->execute()){
            echo "<script>
                    alert('Registration Successful! Waiting for Admin Approval');
                    window.location='../login.php';
                  </script>";
        } else {
            echo "Photography Error: " . $photo_stmt->error;
        }

        $photo_stmt->close();

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
<title>Photography Registration | EventSpace</title>
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

<h3 class="register-title">Photography & Videography Vendor Registration</h3>

<form method="POST" enctype="multipart/form-data">

<!-- BASIC INFO -->
<div class="section-title">Basic Information</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Business Name</label>
        <input type="text" name="business_name" class="form-control" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Owner Name</label>
        <input type="text" name="owner_name" class="form-control" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" required>
    </div>
</div>

<!-- ADDRESS -->
<div class="section-title">Business Address</div>

<div class="mb-3">
    <label>Street</label>
    <input type="text" name="street" class="form-control" required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>City</label>
        <input type="text" name="city" class="form-control" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Pincode</label>
        <input type="text" name="pincode" class="form-control" required>
    </div>
</div>

<!-- PHOTOGRAPHY DETAILS -->
<div class="section-title">Photography & Video Services</div>

<div class="mb-3">
<label>Services Offered</label>
<div class="row">

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="Wedding Photography" id="wedding">
<label class="form-check-label" for="wedding">Wedding Photography</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="Pre-Wedding Shoot" id="prewedding">
<label class="form-check-label" for="prewedding">Pre-Wedding Shoot</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="Candid Photography" id="candid">
<label class="form-check-label" for="candid">Candid Photography</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="Traditional Photography" id="traditional">
<label class="form-check-label" for="traditional">Traditional Photography</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="Event Videography" id="video">
<label class="form-check-label" for="video">Event Videography</label>
</div>
</div>

<div class="col-md-4 col-6">
<div class="form-check">
<input class="form-check-input" type="checkbox" name="services[]" value="Drone Shoot" id="drone">
<label class="form-check-label" for="drone">Drone Shoot</label>
</div>
</div>

</div>
</div>

<div class="mb-3">
<label>Starting Package Price (₹)</label>
<input type="number" class="form-control" name="starting_price" required>
</div>

<div class="mb-3">
<label>Equipment Type</label>
<select class="form-select" name="equipment_type" required>
<option value="">Select</option>
<option>Basic DSLR</option>
<option>Professional DSLR</option>
<option>Mirrorless Camera</option>
<option>4K Video Setup</option>
<option>Drone + 4K Setup</option>
</select>
</div>

<div class="mb-3">
<label>Packages Offered</label>
<textarea class="form-control" name="packages" rows="3" placeholder="Mention package names with pricing details" required></textarea>
</div>

<div class="mb-3">
<label>Years of Experience</label>
<input type="number" class="form-control" name="experience" required>
</div>

<div class="mb-3">
<label>Portfolio Images (Upload Multiple)</label>
<input type="file" class="form-control" name="portfolio_images[]" multiple required>
</div>

<div class="mb-3">
<label>About Your Photography Service</label>
<textarea class="form-control" name="about_business" rows="4" required></textarea>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
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
</body>
</html>