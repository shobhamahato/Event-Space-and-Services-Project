<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/db.php');

$uid = $_SESSION['user_id'];

/* FETCH USER */
$result = mysqli_query($conn,"SELECT * FROM users WHERE id='$uid'");
$user = mysqli_fetch_assoc($result);

$userName = $user['name'];

/* UPDATE PROFILE */
if(isset($_POST['update_profile'])){

    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $mobile = mysqli_real_escape_string($conn,$_POST['mobile']);
    $address = mysqli_real_escape_string($conn,$_POST['address']);

    $update = "UPDATE users SET 
               name='$name',
               email='$email',
               mobile='$mobile',
               address='$address'
               WHERE id='$uid'";

    if(mysqli_query($conn,$update)){

        $_SESSION['success'] = "Profile Updated Successfully";

        header("Location: profile.php");
        exit();

    }else{

        $error = "Something went wrong!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Profile</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
    background:linear-gradient(135deg,#eef2ff,#f8fafc);
    font-family:'Poppins',sans-serif;
    overflow-x:hidden;
}

/* ================= NAVBAR ================= */

.navbar{
    background:#111827;
    padding:15px 25px;
    border-radius:18px;
    margin:12px;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
}

.navbar-brand{
    color:white !important;
    font-size:26px;
    font-weight:700;
    text-decoration:none;
}

.logo-icon{
    color:#1cc88a;
}

.profile{
    display:flex;
    align-items:center;
    gap:10px;
    background:rgba(255,255,255,0.08);
    padding:6px 12px;
    border-radius:50px;
    cursor:pointer;
}

.profile img{
    width:42px;
    height:42px;
    border-radius:50%;
    border:2px solid #1cc88a;
}

.profile span{
    color:white;
    font-weight:600;
}

/* ================= FORM SECTION ================= */

.edit-container{
    width:95%;
    max-width:1100px;
    margin:auto;
    padding:30px 0;
}

.edit-card{
    background:rgba(255,255,255,0.75);
    backdrop-filter:blur(10px);
    border-radius:30px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.edit-header{
    background:linear-gradient(to right,#0f2027,#203a43,#2c5364);
    padding:40px;
    text-align:center;
    color:white;
}

.edit-header img{
    width:110px;
    height:110px;
    border-radius:50%;
    border:5px solid white;
    margin-bottom:15px;
}

.edit-header h2{
    font-weight:700;
    margin-bottom:5px;
}

.edit-body{
    padding:40px;
}

.form-label{
    font-weight:600;
    color:#203a43;
    margin-bottom:8px;
}

.form-control{
    border:none;
    border-radius:14px;
    padding:14px;
    background:#f8fafc;
    box-shadow:0 5px 15px rgba(0,0,0,0.04);
}

.form-control:focus{
    border:2px solid #203a43;
    box-shadow:none;
}

.btn-save{
    background:linear-gradient(to right,#0f2027,#203a43,#2c5364);
    border:none;
    color:white;
    padding:14px 35px;
    border-radius:14px;
    font-weight:600;
    transition:0.3s;
}

.btn-save:hover{
    color:white;
    transform:translateY(-3px);
    box-shadow:0 10px 20px rgba(0,0,0,0.15);
}

.btn-back{
    background:#e5e7eb;
    color:#111827;
    padding:14px 35px;
    border-radius:14px;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}

.btn-back:hover{
    background:#d1d5db;
    color:#111827;
}

.alert{
    border-radius:14px;
}

@media(max-width:768px){

    .edit-body{
        padding:25px;
    }

}

</style>

</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="navbar d-flex justify-content-between align-items-center">

    <!-- LOGO -->
    <a class="navbar-brand" href="dashboard.php">

        <i class="fa fa-calendar-check me-2 logo-icon"></i>

        EventSpace

    </a>

    <!-- PROFILE -->
    <div class="profile">

        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=1cc88a&color=fff">

        <span>
            <?php echo htmlspecialchars($userName); ?>
        </span>

    </div>

</nav>

<!-- ================= EDIT PROFILE ================= -->

<div class="edit-container">

    <div class="edit-card">

        <!-- HEADER -->
        <div class="edit-header">

            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=1cc88a&color=fff&size=200">

            <h2>Edit Profile</h2>

            <p class="mb-0">
                Update your personal information
            </p>

        </div>

        <!-- BODY -->
        <div class="edit-body">

            <?php if(isset($error)){ ?>

                <div class="alert alert-danger">

                    <?php echo $error; ?>

                </div>

            <?php } ?>

            <form method="POST">

                <div class="row g-4">

                    <!-- NAME -->
                    <div class="col-md-6">

                        <label class="form-label">

                            Full Name

                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="<?php echo htmlspecialchars($user['name']); ?>"
                               required>

                    </div>

                    <!-- EMAIL -->
                    <div class="col-md-6">

                        <label class="form-label">

                            Email Address

                        </label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               value="<?php echo htmlspecialchars($user['email']); ?>"
                               required>

                    </div>

                    <!-- MOBILE -->
                    <div class="col-md-6">

                        <label class="form-label">

                            Mobile Number

                        </label>

                        <input type="text"
                               name="mobile"
                               class="form-control"
                               value="<?php echo htmlspecialchars($user['mobile']); ?>">

                    </div>

                    <!-- ADDRESS -->
                    <div class="col-12">

                        <label class="form-label">

                            Address

                        </label>

                        <textarea name="address"
                                  class="form-control"
                                  rows="4"><?php echo htmlspecialchars($user['address']); ?></textarea>

                    </div>

                </div>

                <!-- BUTTONS -->
                <div class="d-flex gap-3 mt-5">

                    <button type="submit"
                            name="update_profile"
                            class="btn btn-save">

                        <i class="bi bi-check-circle me-2"></i>

                        Save Changes

                    </button>

                    <a href="profile.php"
                       class="btn-back">

                        <i class="bi bi-arrow-left me-2"></i>

                        Back

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>