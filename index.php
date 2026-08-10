<?php include 'includes/header.php'; ?>
<style>
/* EVENT CATEGORY CARD HOVER EFFECT */
.card{
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover{
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 18px 35px rgba(0,0,0,0.3);
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
</style>
<!-- HERO SECTION -->
<section class="d-flex align-items-center text-white"
style="background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
url('./assets/images/index_page/hero6.avif') center/cover no-repeat;
height: 95vh;">

    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-4">Make Your Events Unforgettable</h1>
        <p class="lead mb-4">Book Venues, Hire Vendors & Manage Everything in One Place</p>
        <a href="auth/register.php" class="btn btn-success btn-lg px-5 rounded-pill me-3 shadow">
            Get Started
        </a>
        <a href="about.php" class="btn btn-outline-light btn-lg px-5 rounded-pill shadow">
            Learn More
        </a>
    </div>
</section>


<!-- EVENT CATEGORIES -->
<section class="py-5 bg-light ">
    <div class="container text-center">
        <h2 class="fw-bold mb-5">Explore Event Categories</h2>

        <div class="row g-4">

            <div class="col-md-3">
                <div class="card shadow border-0 rounded-4 overflow-hidden">
                    <!-- <img src="https://images.unsplash.com/photo-1519741497674-611481863552"
                         class="card-img-top" style="height:220px; object-fit:cover;"> -->
                    <img src="./assets/images/index_page/wedding.webp"
                         class="card-img-top" style="height:220px; object-fit:cover;">
                    <div class="card-body">
                        <h5 class="fw-bold">Weddings</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow border-0 rounded-4 overflow-hidden">
                    <img src="./assets/images/index_page/birthday1.webp"
                         class="card-img-top" style="height:220px; object-fit:cover;">
                    <div class="card-body">
                        <h5 class="fw-bold">Birthdays</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow border-0 rounded-4 overflow-hidden">
                    <img src="./assets/images/index_page/corporate.jpg"
                         class="card-img-top" style="height:220px; object-fit:cover;">
                    <div class="card-body">
                        <h5 class="fw-bold">Corporate</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow border-0 rounded-4 overflow-hidden">
                    <img src="./assets/images/index_page/privateParty.jpg"
                         class="card-img-top" style="height:220px; object-fit:cover;">
                    <div class="card-body">
                        <h5 class="fw-bold">Private Parties</h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- WHY CHOOSE US -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-5">Why Choose EventSpace?</h2>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="p-4 shadow rounded-4">
                    <h4 class="fw-bold">Verified Vendors</h4>
                    <p>We connect you with trusted and experienced professionals.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 shadow rounded-4">
                    <h4 class="fw-bold">Easy Booking</h4>
                    <p>Simple booking process with secure payment integration.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 shadow rounded-4">
                    <h4 class="fw-bold">24/7 Support</h4>
                    <p>Our team is always ready to assist you anytime.</p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- STATISTICS SECTION -->
<section class="py-5 text-white text-center"
style="background: linear-gradient(135deg, #1cc88a, #0f9b0f);">
    <div class="container">
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


<!-- GALLERY SECTION -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="fw-bold mb-5">Memorable Moments</h2>

        <div class="row g-4">
            <div class="col-md-3">
                <img src="./assets/images/index_page/beach.avif"
                     class="img-fluid rounded-4 shadow-lg"
                     style="height:250px; object-fit:cover;">
            </div>

            <div class="col-md-3">
                <img src="./assets/images/index_page/gallery7.jpg"
                     class="img-fluid rounded-4 shadow-lg"
                     style="height:250px; object-fit:cover;">
            </div>

            <div class="col-md-3">
                <img src="./assets/images/index_page/beach1.jpg"
                     class="img-fluid rounded-4 shadow-lg"
                     style="height:250px; object-fit:cover;">
            </div>

            <div class="col-md-3">
                <!-- <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30" -->
                <img src="./assets/images/index_page/stage.jpg"
                     class="img-fluid rounded-4 shadow-lg"
                     style="height:250px; object-fit:cover; width:300px">
            </div>
        </div>
    </div>
</section>


<!-- FINAL PREMIUM CTA -->
<section class="py-5 text-white text-center"
style="background: linear-gradient(135deg, #0f2027, #1cc88a);">
    <div class="container">
        <h2 class="fw-bold mb-3">Let’s Create Something Beautiful Together</h2>
        <p class="mb-4">From weddings to corporate events — we handle everything with perfection.</p>

        <a href="auth/register.php"
           class="btn btn-light btn-lg rounded-pill px-5 shadow">
            Start Planning Now
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>