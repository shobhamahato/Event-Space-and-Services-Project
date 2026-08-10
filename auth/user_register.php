<?php
session_start();
include("../config/db.php");

/* =========================
   RUN ONLY WHEN FORM SUBMITTED
========================= */
if($_SERVER["REQUEST_METHOD"] != "POST"){
    header("Location: register.php");
    exit();
}

/* =========================
   GET FORM DATA
========================= */
$name     = trim($_POST['fullname']);
$email    = trim($_POST['email']);
$mobile   = trim($_POST['phone']);
$address  = trim($_POST['address']);
$password = $_POST['password'];
$confirm  = $_POST['confirm_password'];

/* =========================
   VALIDATION
========================= */
if(empty($name) || empty($email) || empty($mobile) || empty($address) || empty($password)){
    die("All fields are required!");
}

if($password !== $confirm){
    die("Passwords do not match!");
}

/* =========================
   CHECK EMAIL EXISTS
========================= */
$check = $conn->prepare("SELECT id FROM users WHERE email=?");

if(!$check){
    die("Check Query Error: " . $conn->error);
}

$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if($result->num_rows > 0){
    die("Email already registered!");
}

/* =========================
   INSERT USER (NO ENCRYPTION)
========================= */
$sql = "INSERT INTO users (name,email,password,mobile,address)
        VALUES (?,?,?,?,?)";

$stmt = $conn->prepare($sql);

if(!$stmt){
    die("Insert Query Error: " . $conn->error);
}

$stmt->bind_param(
    "sssss",
    $name,
    $email,
    $password,
    $mobile,
    $address
);

if($stmt->execute()){
    header("Location: login.php?register=success");
    exit();
} else {
    die("Execute Error: " . $stmt->error);
}

$conn->close();
?>