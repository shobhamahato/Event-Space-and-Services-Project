
<!DOCTYPE html>
<html>

<head>

<title>Booking Form</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    background:
    linear-gradient(135deg,#fad2e1,#cdb4db,#bde0fe);
    min-height:100vh;
}

.booking-container{
    padding:50px 0;
}

.form-box{
    background:rgba(255,255,255,0.88);
    backdrop-filter:blur(12px);

    border-radius:30px;

    padding:45px;

    box-shadow:0 15px 40px rgba(0,0,0,0.12);

    border:1px solid rgba(255,255,255,0.4);
}

.vendor-badge{
    display:inline-block;

    background:linear-gradient(135deg,#ff758f,#ffb3c6);

    color:white;

    padding:10px 22px;

    border-radius:40px;

    font-size:14px;

    font-weight:600;

    margin-bottom:20px;
}

.title{
    font-size:34px;
    font-weight:700;
    color:#4a4e69;
}

.subtitle{
    color:#6c757d;
    margin-bottom:35px;
}

.form-label{
    font-weight:600;
    color:#4a4e69;
    margin-bottom:8px;
}

.form-control,
.form-select{
    border:none;
    border-radius:15px;
    padding:14px 16px;
    background:#f8f9ff;
    box-shadow:inset 0 2px 5px rgba(0,0,0,0.03);
    transition:0.3s;
}

.form-control:focus,
.form-select:focus{
    border:none;
    background:white;
    box-shadow:0 0 0 4px rgba(205,180,219,0.35);
}

textarea.form-control{
    resize:none;
}

.info-card{
    background:linear-gradient(135deg,#ffffff,#f8f0ff);

    border-radius:20px;

    padding:20px;

    margin-bottom:30px;

    border:1px solid #eee;
}

.info-title{
    font-size:18px;
    font-weight:700;
    color:#5a189a;
    margin-bottom:12px;
}

.info-item{
    margin-bottom:8px;
    color:#555;
}

.info-item strong{
    color:#222;
}

.btn-submit{
    background:linear-gradient(135deg,#ff758f,#c77dff);

    border:none;

    padding:15px;

    border-radius:16px;

    color:white;

    font-size:17px;

    font-weight:600;

    transition:0.3s;
}

.btn-submit:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 25px rgba(199,125,255,0.35);
}

.section-heading{
    font-size:20px;
    font-weight:700;
    color:#5a189a;
    margin-bottom:20px;
}

.back-btn{
    background:#4a4e69;
    color:white;
    padding:10px 24px;
    border-radius:50px;
    text-decoration:none;
    font-weight:500;
    transition:0.3s;
    display:inline-block;
}

.back-btn:hover{
    background:#22223b;
    color:white;
    transform:translateX(-3px);
}

.amount-field{
    background:#ecebff !important;
    color:#4f46e5;
    font-weight:700;
    cursor:not-allowed;
}

@media(max-width:768px){

    .form-box{
        padding:25px;
    }

    .title{
        font-size:26px;
    }

}

</style>

</head>

<body>

<div class="container booking-container">

<div class="row justify-content-center">

<div class="col-lg-9">

<!-- BACK BUTTON -->

<div class="mb-4">

<a href="cart.php" class="back-btn">

← Back

</a>

</div>

<div class="form-box">

<span class="vendor-badge">

<?php echo ucfirst($vendor['vendor_type']); ?>

</span>

<h2 class="title">

Book <?php echo $vendor['business_name']; ?>

</h2>

<p class="subtitle">

Fill in your event details and send your booking request instantly.

</p>

<div class="info-card">

<div class="info-title">
Vendor Information
</div>

<div class="row">

<div class="col-md-6">

<div class="info-item">
<strong>Business:</strong>
<?php echo $vendor['business_name']; ?>
</div>

<div class="info-item">
<strong>Service:</strong>
<?php echo $service_name; ?>
</div>

</div>

<div class="col-md-6">

<div class="info-item">
<strong>Status:</strong>
Available for Booking
</div>

</div>

</div>

</div>

<form method="POST">

<div class="section-heading">
Personal Details
</div>

<div class="row">

<div class="col-md-6 mb-4">

<label class="form-label">
Full Name
</label>

<input
type="text"
name="customer_name"
class="form-control"
placeholder="Enter your full name"
required>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">
Phone Number
</label>

<input
type="text"
name="customer_phone"
class="form-control"
placeholder="Enter phone number"
required>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">
Email Address
</label>

<input
type="email"
name="customer_email"
class="form-control"
placeholder="Enter email address"
required>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">
Event Type
</label>

<select
name="event_type"
class="form-select"
required>

<option value="">
Select Event Type
</option>

<option>Wedding</option>

<option>Birthday</option>

<option>Corporate Event</option>

<option>Reception</option>

<option>Anniversary</option>

<option>Engagement</option>

<option>Other</option>

</select>

</div>

</div>

<div class="section-heading">
Event Details
</div>

<div class="row">

<div class="col-md-6 mb-4">

<label class="form-label">
Event Date
</label>

<input
type="date"
name="event_date"
class="form-control"
required>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">
Expected Guest Count
</label>

<input
type="number"
name="guest_count"
class="form-control"
placeholder="Number of guests"
required>

</div>

<!-- AMOUNT FIELD -->

<div class="col-md-6 mb-4">

<label class="form-label">
Booking Amount
</label>

<input
type="text"
class="form-control amount-field"
value="₹<?php echo number_format($service_price,2); ?>"
disabled>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">
Start Time
</label>

<input
type="time"
name="start_time"
class="form-control"
required>

</div>

<div class="col-md-6 mb-4">

<label class="form-label">
End Time
</label>

<input
type="time"
name="end_time"
class="form-control"
required>

</div>

<div class="col-12 mb-4">

<label class="form-label">
Event Venue / Location
</label>

<input
type="text"
name="event_location"
class="form-control"
placeholder="Enter complete event location"
required>

</div>

<div class="col-12 mb-4">

<label class="form-label">
Special Requirements
</label>

<textarea
name="requirements"
class="form-control"
rows="5"
placeholder="Write decoration theme, food preference, timing details, etc."></textarea>

</div>

<div class="col-12">

<button
type="submit"
name="submit_booking"
class="btn btn-submit w-100">

Send Booking Request

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</body>
</html>