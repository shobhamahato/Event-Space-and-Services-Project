<?php include 'includes/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(180deg,#fff6fb,#f3e9ff);
}

/* HERO */
.hero{
height:95vh;
background:
linear-gradient(rgba(0,0,0,0.45),rgba(0,0,0,0.45)),
url('./assets/images/index_page/hero2.jpg') center/cover no-repeat;
position:relative;
}

/* floating decoration */
.hero::before{
content:"";
position:absolute;
top:40px;
left:40px;
width:200px;
height:200px;
background:#ffd6ec;
border-radius:50%;
opacity:0.8;
}

.hero::after{
content:"";
position:absolute;
bottom:60px;
right:60px;
width:250px;
height:250px;
background:#d9c6ff;
border-radius:50%;
opacity:0.7 ;
}

/* buttons */

.btn-success{
background:#ff6fa8;
border:none;
}

.btn-success:hover{
background:#ff3f8f;
}

/* section title */

.section-title{
font-weight:700;
color:#7a3cff;
}

/* CATEGORY SECTION */

.category{
background:linear-gradient(135deg,#ffe8f5,#efe6ff);
border-radius:40px;
}

/* glass cards */

.card{
background:rgba(255,255,255,0.7);
backdrop-filter:blur(10px);
border:none;
border-radius:20px;
transition:0.4s;
}

.card:hover{
transform:translateY(-12px) scale(1.05);
box-shadow:0 20px 45px rgba(0,0,0,0.2);
}

/* WHY CHOOSE */

.why-card{
background:white;
border-radius:25px;
transition:0.4s;
}

.why-card:hover{
transform:translateY(-10px);
box-shadow:0 15px 35px rgba(0,0,0,0.15);
}

/* STATS */

.stats{
background:linear-gradient(135deg,#ff9ad5,#b79dff);
border-radius:40px;
}

/* gallery */

.gallery img{
transition:0.4s;
border-radius:20px;
}

.gallery img:hover{
transform:scale(1.08);
box-shadow:0 15px 40px rgba(0,0,0,0.2);
}

/* CTA */

.cta{
background:linear-gradient(135deg,#ffb6e6,#c7b6ff);
border-radius:40px;
}
/* HERO SECTION */

.hero-section{
height:95vh;
display:flex;
align-items:center;
justify-content:center;

background:
linear-gradient(rgba(255,240,248,0.85), rgba(240,230,255,0.85)),
url('./assets/images/index_page/hero2.jpg');

background-size:cover;
background-position:center;
background-repeat:no-repeat;
}

/* Hero Text */

.hero-title{
font-size:60px;
font-weight:700;
color:#7a3cff;
}

.hero-text{
font-size:20px;
color:#555;
}

/* Hero Buttons */

.btn-primary{
background:#ff7db8;
border:none;
}

.btn-primary:hover{
background:#ff4da0;
}
</style>

<!-- HERO -->
<!-- HERO SECTION -->
<section class="hero-section d-flex align-items-center text-center">

<div class="container">

<h1 class="hero-title mb-4">
Make Your Events Unforgettable
</h1>

<p class="hero-text mb-4">
Book Venues, Hire Vendors & Manage Everything in One Place
</p>

<a href="auth/register.php" class="btn btn-primary btn-lg px-5 rounded-pill me-3 shadow">
Get Started
</a>

<a href="about.php" class="btn btn-outline-dark btn-lg px-5 rounded-pill shadow">
Learn More
</a>

</div>

</section>

<!-- EVENT CATEGORIES -->
<section class="py-5">

<div class="container category p-5 text-center">

<h2 class="section-title mb-5">
Explore Event Categories
</h2>

<div class="row g-4">

<div class="col-md-3">
<div class="card shadow overflow-hidden">
<img src="./assets/images/index_page/wedding.webp" class="card-img-top" style="height:220px;object-fit:cover;">
<div class="card-body">
<h5 class="fw-bold">Weddings</h5>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow overflow-hidden">
<img src="./assets/images/index_page/birthday1.webp" class="card-img-top" style="height:220px;object-fit:cover;">
<div class="card-body">
<h5 class="fw-bold">Birthdays</h5>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow overflow-hidden">
<img src="./assets/images/index_page/corporate.jpg" class="card-img-top" style="height:220px;object-fit:cover;">
<div class="card-body">
<h5 class="fw-bold">Corporate</h5>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow overflow-hidden">
<img src="./assets/images/index_page/privateParty.jpg" class="card-img-top" style="height:220px;object-fit:cover;">
<div class="card-body">
<h5 class="fw-bold">Private Parties</h5>
</div>
</div>
</div>

</div>

</div>
</section>

<!-- WHY CHOOSE -->
<section class="py-5">

<div class="container text-center">

<h2 class="section-title mb-5">
Why Choose EventSpace?
</h2>
<div class="row g-4">

<div class="col-md-4">
<div class="p-4 shadow why-card">
<h4 class="fw-bold">Verified Vendors</h4>
<p>We connect you with trusted and experienced professionals.</p>
</div>
</div>

<div class="col-md-4">
<div class="p-4 shadow why-card">
<h4 class="fw-bold">Easy Booking</h4>
<p>Simple booking process with secure payment integration.</p>
</div>
</div>

<div class="col-md-4">
<div class="p-4 shadow why-card">
<h4 class="fw-bold">24/7 Support</h4>
<p>Our team is always ready to assist you anytime.</p>
</div>
</div>

</div>

</div>
</section>

<!-- STATS -->

<section class="py-5 text-white text-center">

<div class="container stats p-5">

<div class="row">

<div class="col-md-3">
<h2 class="fw-bold">500+</h2>
<p>Events Organized</p>
</div>

<div class="col-md-3">
<h2 class="fw-bold">200+</h2>
<p>Trusted Vendors</p>
</div>

<div class="col-md-3">
<h2 class="fw-bold">1000+</h2>
<p>Happy Clients</p>
</div>

<div class="col-md-3">
<h2 class="fw-bold">4.9★</h2>
<p>Average Rating</p>
</div>

</div>

</div>
</section>

<!-- GALLERY -->

<section class="py-5 gallery">

<div class="container text-center">

<h2 class="section-title mb-5">
Memorable Moments
</h2>

<div class="row g-4">

<div class="col-md-3">
<img src="./assets/images/index_page/beach.avif" class="img-fluid shadow-lg" style="height:250px;object-fit:cover;">
</div>

<div class="col-md-3">
<img src="./assets/images/index_page/gallery7.jpg" class="img-fluid shadow-lg" style="height:250px;object-fit:cover;">
</div>

<div class="col-md-3">
<img src="./assets/images/index_page/beach1.jpg" class="img-fluid shadow-lg" style="height:250px;object-fit:cover;">
</div>

<div class="col-md-3">
<img src="./assets/images/index_page/stage.jpg" class="img-fluid shadow-lg" style="height:250px;object-fit:cover;">
</div>

</div>

</div>

</section>

<!-- CTA -->

<section class="py-5 text-center text-white">

<div class="container cta p-5">

<h2 class="fw-bold mb-3">
Let’s Create Something Beautiful Together
</h2>

<p class="mb-4">
From weddings to corporate events — we handle everything with perfection.
</p>

<a href="auth/register.php"
class="btn btn-light btn-lg rounded-pill px-5 shadow">
Start Planning Now
</a>

</div>

</section>

<?php include 'includes/footer.php'; ?>