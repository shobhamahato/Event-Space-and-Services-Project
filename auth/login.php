<?php 
session_start();
include("../config/db.php");

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    //    1️⃣ CHECK ADMIN LOGIN


    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $admin = $result->fetch_assoc();

        if($password === $admin['password']){

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];

            header("Location: ../admin/dashboard.php");
            exit();

        }else{
            $error = "Invalid Admin Password!";
        }

    } 
    /* =========================
       2️⃣ CHECK USER LOGIN
    ========================== */

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $user = $result->fetch_assoc();

        if($password === $user['password']){

            $_SESSION['user_id'] = $user['id'];   
            $_SESSION['user_name'] = $user['name'];

            header("Location: ../user/dashboard.php");
            // header("Location: ../user/dashboard2.php");
            exit();

        }else{
            $error = "Invalid Password!";
        }

    }else{

        /* CHECK VENDOR LOGIN */

        $stmt = $conn->prepare("SELECT * FROM vendors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){

            $vendor = $result->fetch_assoc();

            if($password === $vendor['password']){

                if($vendor['status'] === 'approved'){

                    $_SESSION['vendor_id'] = $vendor['vendor_id'];
                    $_SESSION['vendor_type'] = $vendor['vendor_type'];
                    $_SESSION['business_name'] = $vendor['business_name'];

                    /* REDIRECT BASED ON VENDOR TYPE */

                    if($vendor['vendor_type'] === 'card_vendor'){
                        
                        header("Location: ../vendor/dashboard.php");

                    }else{

                        header("Location: ../vendor/dashboard.php");
                    }

                    exit();

                }else{
                    $error = "Your account is not approved yet!";
                }

            }else{
                $error = "Invalid Password!";
            }

        }else{
            $error = "Email not registered!";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<?php include '../includes/header.php'; ?>

<section class="d-flex align-items-center justify-content-center"
style="min-height: 90vh;
background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
url('https://images.unsplash.com/photo-1505373877841-8d25f7d46678') center/cover no-repeat;">

<div class="card p-4 shadow-lg" style="width:400px;border-radius:15px;">

<h3 class="text-center fw-bold mb-4">Login to EventSpace</h3>

<?php if(isset($_GET['register']) && $_GET['register']=="success"){ ?>
<div class="alert alert-success text-center">
Registration successful! Please login.
</div>
<?php } ?>

<?php if(!empty($error)){ ?>
<div class="alert alert-danger text-center">
<?php echo $error; ?>
</div>
<?php } ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="d-grid">
<button type="submit" name="login" class="btn btn-success rounded-pill">
Login
</button>
</div>

<!-- <div class="text-center mt-3">
<a href="#" class="text-decoration-none">Forgot Password?</a>
</div> -->

<div class="text-center mt-2">
Don't have an account?
<a href="register.php" class="fw-bold text-success text-decoration-none">
Register
</a>
</div>

</form>

</div>
</section>

<?php include '../includes/footer.php'; ?>