<?php
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
    $decoration_types = isset($_POST['decoration_types']) 
                        ? implode(",", $_POST['decoration_types']) 
                        : "";

    $flowers = $_POST['flowers'];
    $packages = $_POST['packages'];
    $starting_price = $_POST['starting_price'];
    $experience = $_POST['experience'];
    $about_business = $_POST['about_business'];

    // ==========================
    // INSERT INTO vendors TABLE
    // ==========================
    $vendor_type = "decorator";

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

                $upload_dir = "../../uploads/decorators/";

                // create folder if not exists
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
        // INSERT INTO decorators TABLE
        // ==========================
        $decorator_stmt = $conn->prepare("INSERT INTO decorators
            (vendor_id, street, city, pincode, decoration_types, flowers, packages, starting_price, experience, portfolio_images, about_business)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $decorator_stmt->bind_param("issssssdiss",
            $vendor_id,
            $street,
            $city,
            $pincode,
            $decoration_types,
            $flowers,
            $packages,
            $starting_price,
            $experience,
            $portfolio_images,
            $about_business
        );

        if($decorator_stmt->execute()){
            echo "<script>
                    alert('Registration Successful! Waiting for Admin Approval');
                    window.location='../login.php';
                  </script>";
        } else {
            echo "Decorator Error: " . $decorator_stmt->error;
        }

        $decorator_stmt->close();

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
<title>Decorator Registration | EventSpace</title>
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
.form-control {
    border-radius: 10px;
    padding: 10px;
}
textarea { resize: none; }
.btn-register {
    background-color: #1cc88a;
    color: white;
    border-radius: 30px;
    padding: 12px;
    font-weight: 600;
    font-size: 16px;
}
.btn-register:hover { background-color: #17a673; }
.small-text { font-size: 13px; }
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
<h3 class="register-title">Decorator Vendor Registration</h3>

<form method="POST" enctype="multipart/form-data">

<div class="section-title">Basic Information</div>

<input type="text" name="business_name" class="form-control mb-3" placeholder="Business Name" required>
<input type="text" name="owner_name" class="form-control mb-3" placeholder="Owner Name" required>
<input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
<input type="text" name="phone" class="form-control mb-3" placeholder="Phone" required>

<div class="section-title">Address</div>

<input type="text" name="street" class="form-control mb-3" placeholder="Street" required>
<input type="text" name="city" class="form-control mb-3" placeholder="City" required>
<input type="text" name="pincode" class="form-control mb-3" placeholder="Pincode" required>

<div class="section-title">Services</div>

<label class="mb-2">Decoration Types Offered</label>

<div class="row mb-3">
<?php
$types = [
"Wedding Stage Decoration",
"Floral Decoration",
"Theme Decoration",
"Birthday Decoration",
"Corporate Event Decoration",
"Engagement Decoration",
"Reception Decoration"
];

foreach($types as $type){
echo "<div class='col-md-6'>
<div class='form-check'>
<input class='form-check-input' type='checkbox' name='decoration_types[]' value='$type'>
<label class='form-check-label'>$type</label>
</div></div>";
}
?>
</div>

<input type="text" name="flowers" class="form-control mb-3" placeholder="Flowers Used">
<textarea name="packages" class="form-control mb-3" placeholder="Packages Details"></textarea>
<input type="number" name="starting_price" class="form-control mb-3" placeholder="Starting Price" required>
<input type="number" name="experience" class="form-control mb-3" placeholder="Experience (Years)" required>
<input type="file" name="portfolio_images[]" class="form-control mb-3" multiple required>
<textarea name="about_business" class="form-control mb-3" placeholder="About Business" required></textarea>
<input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

<button type="submit" name="submit" class="btn btn-register w-100">Submit for Approval</button>

<div class="text-center mt-3 small-text">
After submission, admin will review and approve your account.
</div>

</form>
</div>
</div>

</body>
</html>