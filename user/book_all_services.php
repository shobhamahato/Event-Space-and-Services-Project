<?php
session_start();

$conn = new mysqli("localhost","root","","event_management_system");

/* ================= STORE STEP 1 FORM DATA ================= */

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $_SESSION['book_all_step1'] = $_POST;

}

/* ================= CART DATA ================= */

$cart = $_SESSION['cart'] ?? [];
?>

<!DOCTYPE html>
<html>
<head>
<title>Book Services - Step 2</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
}

.card-box{
    background:#fff;
    border-radius:16px;
    padding:25px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.section-card{
    border-radius:14px;
    padding:15px;
    background:#fff;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    margin-bottom:15px;
}

.section-title{
    font-weight:600;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.badge-soft{
    background:#e7f1ff;
    color:#0d6efd;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
}

input{
    border-radius:10px !important;
    padding:10px !important;
}

.btn-submit{
    border-radius:12px;
    padding:12px;
    font-weight:600;
}
</style>

<script>
function toggleSection(vendor){
    let checkbox = document.getElementById(vendor+"_check");
    let section = document.getElementById(vendor+"_section");

    let inputs = section.querySelectorAll("input");

    if(checkbox.checked){
        section.style.display = "block";
        inputs.forEach(i => i.required = true);
    } else {
        section.style.display = "none";
        inputs.forEach(i => i.required = false);
    }
}
</script>

</head>

<body>

<div class="container mt-5">

<div class="card-box">

<h3 class="text-center mb-4">📦 Choose Your Services</h3>

<form method="POST" action="all_preview.php">

<!-- ================= DECORATOR ================= -->
<div class="section-card">

<div class="section-title">
🎨 Decorator
<span class="badge-soft">Wedding / Event Decor</span>
</div>

<input type="checkbox" id="decorator_check"
name="decorator_enabled"
onclick="toggleSection('decorator')">
<label class="mb-2">Include this service</label>

<div id="decorator_section" style="display:none;" class="mt-2">

    <input name="decorator[theme]" class="form-control mb-2" placeholder="Theme (e.g. Royal / Floral / Modern)">
    <input name="decorator[color]" class="form-control mb-2" placeholder="Color Theme">
    <input name="decorator[flower]" class="form-control mb-2" placeholder="Flower Type">

</div>
</div>

<!-- ================= CATERER ================= -->
<div class="section-card">

<div class="section-title">
🍽 Caterer
<span class="badge-soft">Food Services</span>
</div>

<input type="checkbox" id="caterer_check"
name="caterer_enabled"
onclick="toggleSection('caterer')">
<label class="mb-2">Include this service</label>

<div id="caterer_section" style="display:none;" class="mt-2">

    <input name="caterer[food]" class="form-control mb-2" placeholder="Food Type (Veg / Non-Veg / Buffet)">
    <input name="caterer[cuisine]" class="form-control mb-2" placeholder="Cuisine (Indian / Chinese / Continental)">
     <input name="caterer[plates]" class="form-control mb-2" placeholder="No. of Plates">
</div>
</div>

<!-- ================= VENUE ================= -->
<div class="section-card">

<div class="section-title">
🏛 Venue
<span class="badge-soft">Hall / Outdoor</span>
</div>

<input type="checkbox" id="venue_check"
name="venue_enabled"
onclick="toggleSection('venue')">
<label class="mb-2">Include this service</label>

<div id="venue_section" style="display:none;" class="mt-2">

    <input name="venue[type]" class="form-control mb-2" placeholder="Venue Type (Hall / Lawn / Resort)">
    <input name="venue[capacity]" class="form-control mb-2" placeholder="Capacity (e.g. 200 guests)">

</div>
</div>

<!-- ================= MUSIC ================= -->
<div class="section-card">

<div class="section-title">
🎧 Music & DJ
<span class="badge-soft">Entertainment</span>
</div>

<input type="checkbox" id="music_check"
name="music_enabled"
onclick="toggleSection('music')">
<label class="mb-2">Include this service</label>

<div id="music_section" style="display:none;" class="mt-2">

    <input name="music[type]" class="form-control mb-2" placeholder="DJ / Live Band">
    <input name="music[duration]" class="form-control mb-2" placeholder="Duration (hours)">

</div>
</div>

<!-- ================= PHOTOGRAPHY ================= -->
<div class="section-card">

<div class="section-title">
📸 Photography
<span class="badge-soft">Capture Moments</span>
</div>

<input type="checkbox" id="photography_check"
name="photography_enabled"
onclick="toggleSection('photography')">
<label class="mb-2">Include this service</label>

<div id="photography_section" style="display:none;" class="mt-2">

    <input name="photography[type]" class="form-control mb-2" placeholder="Photo / Video / Drone">
    <input name="photography[duration]" class="form-control mb-2" placeholder="Duration (hours)">

</div>
</div>

<!-- ================= BEAUTY ================= -->
<div class="section-card">

<div class="section-title">
💄 Beauty & Parlour
<span class="badge-soft">Makeup & Styling</span>
</div>

<input type="checkbox" id="beauty_check"
name="beauty_enabled"
onclick="toggleSection('beauty')">
<label class="mb-2">Include this service</label>

<div id="beauty_section" style="display:none;" class="mt-2">

    <input name="beauty[service]" class="form-control mb-2" placeholder="Service (Makeup / Hair / Bridal)">
    <input name="beauty[persons]" class="form-control mb-2" placeholder="Number of Persons">

</div>
</div>

<!-- ================= CARDS ================= -->
<div class="section-card">

<div class="section-title">
💌 Invitation Cards
<span class="badge-soft">Design & Print</span>
</div>

<input type="checkbox" id="cards_check"
name="cards_enabled"
onclick="toggleSection('cards')">
<label class="mb-2">Include this service</label>

<div id="cards_section" style="display:none;" class="mt-2">

    <!-- <input name="cards[type]" class="form-control mb-2" placeholder="Card Design Type"> -->
    <input name="cards[quantity]" class="form-control mb-2" placeholder="Number of cards required">

</div>
</div>

<!-- ================= SUBMIT ================= -->
<button class="btn btn-success w-100 btn-submit">
🚀 Submit Booking Request
</button>

</form>

</div>

</div>

</body>
</html>