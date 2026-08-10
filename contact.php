<?php include 'includes/header.php'; ?>

<!-- Contact Hero -->
<section class="py-5 text-white text-center"
    style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
    url('https://images.unsplash.com/photo-1515168833906-d2a3b82b302a') center/cover no-repeat;">
    <div class="container">
        <h1 class="fw-bold">Contact Us</h1>
        <p class="lead">We would love to hear from you</p>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-5 d-flex align-items-stretch">

            <!-- Contact Form -->
            <div class="col-md-6 d-flex">
                <div class="card w-100 shadow-sm p-4">
                    <h3 class="fw-bold mb-4">Send Us a Message</h3>

                    <form>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" placeholder="Enter your name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" placeholder="Enter your email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" rows="5" placeholder="Write your message"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-md-6 d-flex">
                <div class="card w-100 shadow-sm p-4">
                    <h3 class="fw-bold mb-4">Get In Touch</h3>

                    <p><strong>Address:</strong> Near Silicon University, Patia, Bhubaneswar, Odisha 751024, India</p>
                    <p><strong>Email:</strong> info@silicon.ac.in</p>
                    <p><strong>Phone:</strong> +91 674 2725446</p>

                    <!-- Google Map -->
                    <iframe 
                        src="https://www.google.com/maps?q=Silicon+University+Bhubaneswar&output=embed"
                        width="100%" 
                        height="300"
                        style="border:0; border-radius:10px;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>