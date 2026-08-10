<?php include 'includes/header.php'; ?>

<style>

/* Page Title Section */
.services-top {
    padding: 80px 0 40px;
    text-align: center;
}

.services-top h1 {
    font-weight: 700;
    color: #0f2027;
}

.services-top p {
    color: #555;
}

/* Service Cards */
.service-section {
    padding: 40px 0 80px;
}

.service-card {
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
    transition: 0.4s;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
}

.service-card img {
    height: 230px;
    object-fit: cover;
}

.service-card-body {
    padding: 25px;
}

.service-card-body h5 {
    font-weight: 600;
    color: #0f2027;
}

.service-card-body p {
    color: #666;
    font-size: 14px;
}

.book-btn {
    background: #1cc88a;
    border: none;
    padding: 8px 18px;
    border-radius: 30px;
    color: white;
    transition: 0.3s;
}

.book-btn:hover {
    background: #17a673;
}

</style>

<!-- Page Title -->
<section class="services-top">
    <div class="container">
        <h1>Our Services</h1>
        <p>Explore our event management services</p>
    </div>
</section>

<!-- Services -->
<section class="service-section">
    <div class="container">
        <div class="row g-4">

            <!-- Wedding -->
            <div class="col-md-4">
                <div class="service-card">
                    <img src="./assets/images/index_page/wedding2.webp" class="w-100">
                    <div class="service-card-body">
                        <h5>Wedding Planning</h5>
                        <p>Complete wedding arrangements including venue, decoration, catering and photography.</p>
                        <a href="/EventProject/auth/login.php" class="book-btn">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Birthday -->
            <div class="col-md-4">
                <div class="service-card">
                    <img src="./assets/images/index_page/bday_service.jpeg" class="w-100">
                    <div class="service-card-body">
                        <h5>Birthday Parties</h5>
                        <p>Fun, creative and memorable birthday party setups for all age groups.</p>
                        <a href="/EventProject/auth/login.php" class="book-btn">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Corporate -->
            <div class="col-md-4">
                <div class="service-card">
                    <img src="./assets/images/index_page/corporate2.jpg" class="w-100">
                    <div class="service-card-body">
                        <h5>Corporate Events</h5>
                        <p>Professional management for conferences, meetings, seminars and business events.</p>
                        <a href="/EventProject/auth/login.php" class="book-btn">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Concert -->
            <div class="col-md-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1506157786151-b8491531f063" class="w-100">
                    <div class="service-card-body">
                        <h5>Concert & Shows</h5>
                        <p>Live shows, music concerts and entertainment events with complete arrangements.</p>
                        <a href="/EventProject/auth/login.php" class="book-btn">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Decoration -->
            <div class="col-md-4">
                <div class="service-card">
                    <img src="./assets/images/index_page/deco_services2.webp" class="w-100">
                    <div class="service-card-body">
                        <h5>Decoration Services</h5>
                        <p>Elegant and customized decorations to match your event theme.</p>
                        <a href="/EventProject/auth/login.php" class="book-btn">Book Now</a>
                    </div>
                </div>
            </div>

            <!-- Catering -->
            <div class="col-md-4">
                <div class="service-card">
                    <img src="https://images.unsplash.com/photo-1555244162-803834f70033" class="w-100">
                    <div class="service-card-body">
                        <h5>Catering Services</h5>
                        <p>Delicious menu options and professional catering for all types of events.</p>
                        <a href="/EventProject/auth/login.php" class="book-btn">Book Now</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>