function validateVendorForm(e){

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
    let portfolio = form.elements['portfolio'].files;
    let about = form.elements['about'].value;
    let password = form.elements['password'].value;

    let businessNameError = document.getElementById("businessNameError");
    let ownerNameError = document.getElementById("ownerNameError");
    let emailError = document.getElementById("emailError");
    let phoneError = document.getElementById("phoneError");
    let streetError = document.getElementById("streetError");
    let cityError = document.getElementById("cityError");
    let pincodeError = document.getElementById("pincodeError");
    let experienceError = document.getElementById("experienceError");
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

    // EXPERIENCE
    if(experience === "" || experience < 0){
        experienceError.innerHTML = "Enter valid experience";
        error = true;
    } else {
        experienceError.innerHTML = "";
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
