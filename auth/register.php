
<?php include '../includes/header.php'; ?>

<style>

/* Background */
.register-section {
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(to right, #e9fdf5, #ffffff);
    padding: 60px 15px;
}

/* Card */
.register-card {
    background: #ffffff;
    padding: 45px;
    border-radius: 20px;
    width: 100%;
    max-width: 520px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.08);
}

.register-card h2 {
    font-weight: 700;
    color: #0f2027;
    text-align: center;
    margin-bottom: 30px;
}

/* Input */
.form-control {
    border-radius: 12px;
    padding: 14px;
    border: 1px solid #e0e0e0;
    transition: 0.3s;
}

.form-control:focus {
    border-color: #1cc88a;
    box-shadow: 0 0 0 0.2rem rgba(28,200,138,0.15);
}

/* Error message */
.error {
    color: red;
    font-size: 13px;
    margin-top: 4px;
}

/* Button */
.register-btn {
    background: #1cc88a;
    border: none;
    padding: 12px;
    border-radius: 30px;
    color: white;
    font-weight: 600;
    width: 100%;
    transition: 0.3s;
}

.register-btn:hover {
    background: #17a673;
    transform: translateY(-2px);
}

/* Login link */
.login-link {
    text-align: center;
    margin-top: 20px;
}

.login-link a {
    color: #1cc88a;
    text-decoration: none;
    font-weight: 500;
}

.login-link a:hover {
    text-decoration: underline;
}

</style>

<section class="register-section">
<div class="register-card">

<h2>Create Account</h2>

<form method="post" action="user_register.php" onsubmit="return validateForm()">

<div class="mb-3">
<input type="text" id="fullname" name="fullname" class="form-control" placeholder="Full Name">
<div id="nameError" class="error"></div>
</div>

<div class="mb-3">
<input type="email" id="email" name="email" class="form-control" placeholder="Email Address">
<div id="emailError" class="error"></div>
</div>

<div class="mb-3">
<input type="tel" id="phone" name="phone" class="form-control" placeholder="Phone Number">
<div id="phoneError" class="error"></div>
</div>

<div class="mb-3">
<textarea id="address" name="address" class="form-control" placeholder="Address" rows="3"></textarea>
<div id="addressError" class="error"></div>
</div>

<div class="mb-3">
<input type="password" id="password" name="password" class="form-control" placeholder="Password">
<div id="passwordError" class="error"></div>
</div>

<div class="mb-4">
<input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password">
<div id="confirmError" class="error"></div>
</div>

<button type="submit" class="register-btn">Register</button>

</form>

<div class="login-link">
Already have an account?
<a href="login.php">Login</a>
</div>

</div>
</section>

<script>

function validateForm(){

let name = document.getElementById("fullname").value.trim();
let email = document.getElementById("email").value.trim();
let phone = document.getElementById("phone").value.trim();
let address = document.getElementById("address").value.trim();
let password = document.getElementById("password").value;
let confirm = document.getElementById("confirm_password").value;

let nameRegex = /^[A-Za-z ]+$/;
let emailRegex = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
let phoneRegex = /^[0-9]{10}$/;

let valid = true;

/* Clear previous errors */
document.getElementById("nameError").innerHTML="";
document.getElementById("emailError").innerHTML="";
document.getElementById("phoneError").innerHTML="";
document.getElementById("addressError").innerHTML="";
document.getElementById("passwordError").innerHTML="";
document.getElementById("confirmError").innerHTML="";

/* Name validation */
if(name=="" || !nameRegex.test(name)){
document.getElementById("nameError").innerHTML="Enter valid name (letters only)";
valid=false;
}

/* Email validation */
if(!emailRegex.test(email)){
document.getElementById("emailError").innerHTML="Enter valid email";
valid=false;
}

/* Phone validation */
if(!phoneRegex.test(phone)){
document.getElementById("phoneError").innerHTML="Enter 10 digit phone number";
valid=false;
}

/* Address validation */
if(address==""){
document.getElementById("addressError").innerHTML="Address required";
valid=false;
}

/* Password validation */
if(password.length < 6){
document.getElementById("passwordError").innerHTML="Password must be at least 6 characters";
valid=false;
}

/* Confirm password */
if(password !== confirm){
document.getElementById("confirmError").innerHTML="Passwords do not match";
valid=false;
}

return valid;

}

</script>

<?php include '../includes/footer.php'; ?>

