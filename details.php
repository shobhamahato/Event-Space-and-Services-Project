<?php
session_start();

/* DATABASE CONNECTION */
$conn = new mysqli("localhost", "root", "", "event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* CHECK VENDOR ID */
if(!isset($_GET['vendor_id'])){
    die("Vendor not found");
}

$vendor_id = $_GET['vendor_id'];

/* FETCH VENDOR DETAILS */
$sql = "SELECT * FROM vendors 
        WHERE vendor_id='$vendor_id'";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){
    die("Vendor not found");
}

$vendor = mysqli_fetch_assoc($result);

/* FETCH PORTFOLIO IMAGES */
$portfolio_sql = "SELECT * FROM vendor_portfolio 
                  WHERE vendor_id='$vendor_id'
                  ORDER BY created_at DESC";

$portfolio_result = mysqli_query($conn,$portfolio_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo $vendor['business_name']; ?></title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
    background:#f4f7fb;
    font-family:'Poppins',sans-serif;
}

/* NAVBAR */
.topbar{
    background: linear-gradient(to right,#0f2027,#203a43,#2c5364);
    padding:15px 30px;
    color:white;
}

.topbar h3{
    margin:0;
    font-weight:600;
}

/* MAIN CARD */
.vendor-box{
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

/* INFO */
.vendor-title{
    font-size:32px;
    font-weight:700;
    color:#203a43;
}

.vendor-type{
    background:#1cc88a;
    color:white;
    display:inline-block;
    padding:8px 18px;
    border-radius:30px;
    font-size:14px;
    margin-top:10px;
}

.info-box{
    margin-top:20px;
}

.info-box p{
    font-size:16px;
    margin-bottom:12px;
    color:#555;
}

.info-box i{
    color:#1cc88a;
    width:25px;
}

/* GALLERY */
.gallery-title{
    font-size:24px;
    font-weight:600;
    margin-top:50px;
    margin-bottom:20px;
    color:#203a43;
}

.gallery-img{
    width:100%;
    height:260px;
    object-fit:cover;
    border-radius:18px;
    transition:0.4s;
    box-shadow:0 8px 20px rgba(0,0,0,0.1);
}

.gallery-img:hover{
    transform:scale(1.03);
}

/* BUTTON */
.btn-theme{
    background: linear-gradient(to right,#0f2027,#203a43,#2c5364);
    border:none;
    color:white;
    border-radius:12px;
    padding:12px 25px;
    font-weight:500;
    transition:0.4s;
    text-decoration:none;
    display:inline-block;
}

.btn-theme:hover{
    color:white;
    transform:translateY(-3px);
}

/* MOBILE */
@media(max-width:768px){

    .vendor-title{
        font-size:24px;
    }

    .gallery-img{
        height:200px;
    }

}

</style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar d-flex justify-content-between align-items-center">

    <h3>
        <i class="fa fa-calendar-check me-2"></i>
        EventSpace
    </h3>

    <a href="user_dashboard.php" class="btn btn-light">
        <i class="fa fa-arrow-left me-1"></i>
        Back
    </a>

</div>

<div class="container py-5">

    <!-- VENDOR DETAILS -->
    <div class="vendor-box">

        <div class="row align-items-center">

            <div class="col-md-8">

                <h1 class="vendor-title">
                    <?php echo $vendor['business_name']; ?>
                </h1>

                <div class="vendor-type">
                    <?php echo ucfirst(str_replace("_"," ",$vendor['vendor_type'])); ?>
                </div>

                <div class="info-box">

                    <p>
                        <i class="fa fa-user"></i>
                        <strong>Owner:</strong>
                        <?php echo $vendor['owner_name']; ?>
                    </p>

                    <p>
                        <i class="fa fa-phone"></i>
                        <strong>Phone:</strong>
                        <?php echo $vendor['phone']; ?>
                    </p>

                    <p>
                        <i class="fa fa-envelope"></i>
                        <strong>Email:</strong>
                        <?php echo $vendor['email']; ?>
                    </p>

                    <p>
                            <i class="fa fa-check-circle"></i>
                            <strong>Status:</strong>
                            <?php echo ucfirst($vendor['status']); ?>
                        </p>

                        <a href="book_vendor.php?vendor_id=<?php echo $vendor['vendor_id']; ?>" 
                        class="btn btn-theme mt-3">

                        <i class="fa fa-calendar-check me-2"></i>
                        Book Now

                        </a>

                </div>

            </div>

            <div class="col-md-4 text-center">

                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($vendor['business_name']); ?>&background=1cc88a&color=fff&size=250"
                     class="img-fluid rounded-circle shadow">

            </div>

        </div>

    </div>

    <!-- GALLERY -->
    <h3 class="gallery-title">
        Portfolio Gallery
    </h3>

    <div class="row g-4">

<?php

if(mysqli_num_rows($portfolio_result) > 0){

    while($portfolio = mysqli_fetch_assoc($portfolio_result)){

        $image = "../uploads/portfolio/" . $portfolio['image_path'];

?>

        <div class="col-lg-4 col-md-6">

            <img src="<?php echo $image; ?>"
                 class="gallery-img">

            <?php if(!empty($portfolio['caption'])){ ?>

                <p class="mt-2 text-muted">
                    <?php echo $portfolio['caption']; ?>
                </p>

            <?php } ?>

        </div>

<?php
    }

}else{

    echo "
    <div class='col-12'>
        <div class='alert alert-warning'>
            No portfolio images uploaded yet.
        </div>
    </div>
    ";

}

?>

    </div>

</div>

</body>
</html>