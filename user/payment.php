<?php 

session_start();

$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* CHECK BOOKING ID */

if(!isset($_GET['booking_id'])){
    die("Booking ID Missing");
}

$booking_id = $_GET['booking_id'];

/* FETCH BOOKING */

$query = mysqli_query($conn,
"SELECT * FROM bookings WHERE id='$booking_id'");

if(mysqli_num_rows($query) == 0){
    die("Booking Not Found");
}

$booking = mysqli_fetch_assoc($query);
$vendor_id = $booking['vendor_id'];

$vendor_query = mysqli_query($conn,
"SELECT * FROM vendors WHERE vendor_id='$vendor_id'");

$vendor = mysqli_fetch_assoc($vendor_query);

/* VENDOR DATA */

$business_name = $vendor['business_name'] ?? 'N/A';

$vendor_type = $vendor['vendor_type'] ?? 'N/A';
?>

<!DOCTYPE html>
<html>
<head>

<title>Invoice Payment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:linear-gradient(135deg,#ffdde1,#cdb4db);
    min-height:100vh;
    font-family:'Poppins',sans-serif;
    padding:40px 15px;
}

.invoice-box{
    max-width:900px;
    margin:auto;
    background:white;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,0.12);
}

.invoice-header{
    background:linear-gradient(135deg,#cdb4db,#ffc8dd);
    padding:35px;
    color:#333;
}

.invoice-header h1{
    font-size:34px;
    font-weight:700;
    margin-bottom:8px;
}

.invoice-header p{
    margin:0;
    font-size:15px;
}

.invoice-body{
    padding:35px;
}

.invoice-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    margin-bottom:30px;
}

.invoice-top h3{
    font-weight:700;
    color:#444;
}

.status-badge{
    background:#fef3c7;
    color:#92400e;
    padding:10px 20px;
    border-radius:30px;
    font-size:14px;
    font-weight:600;
}

.section-title{
    font-size:20px;
    font-weight:700;
    margin-bottom:20px;
    color:#5b21b6;
}

.details-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
    margin-bottom:35px;
}

.detail-card{
    background:#faf5ff;
    border-radius:18px;
    padding:20px;
    border:1px solid #eee;
}

.detail-card h6{
    color:#777;
    font-size:13px;
    margin-bottom:10px;
    text-transform:uppercase;
    letter-spacing:1px;
}

.detail-card p{
    margin:0;
    font-size:16px;
    font-weight:600;
    color:#333;
    word-break:break-word;
}

.invoice-table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

.invoice-table thead{
    background:#f8f5ff;
}

.invoice-table th{
    padding:15px;
    font-size:14px;
    color:#555;
}

.invoice-table td{
    padding:18px 15px;
    border-bottom:1px solid #eee;
    font-size:15px;
    color:#444;
}

.total-section{
    margin-top:35px;
    background:#fff0f6;
    padding:25px;
    border-radius:18px;
}

.total-row{
    display:flex;
    justify-content:space-between;
    margin-bottom:14px;
    font-size:16px;
}

.grand-total{
    border-top:2px dashed #d8b4fe;
    padding-top:15px;
    margin-top:15px;
    font-size:24px;
    font-weight:700;
    color:#7c3aed;
}

.pay-btn{
    width:100%;
    border:none;
    background:linear-gradient(135deg,#7b2cbf,#c77dff);
    color:white;
    padding:16px;
    border-radius:14px;
    font-size:18px;
    font-weight:600;
    margin-top:30px;
    transition:0.3s;
}

.pay-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(123,44,191,0.3);
}

.footer-text{
    text-align:center;
    margin-top:20px;
    color:#777;
    font-size:14px;
}

@media(max-width:768px){

    .invoice-header{
        text-align:center;
    }

    .invoice-top{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

}

</style>

</head>

<body>

<div class="invoice-box">

    <!-- HEADER -->

    <div class="invoice-header">

        <h1>Event Booking Invoice</h1>

        <p>Secure Payment Summary</p>

    </div>

    <!-- BODY -->

    <div class="invoice-body">

        <div class="invoice-top">

            <div>

                <h3>Invoice </h3><p><br>Transaction Id : #<?php echo $booking['id']; ?></p>

                <p class="text-muted mb-0">
                    Created On :
                    <?php echo date("d M Y", strtotime($booking['created_at'])); ?>
                </p>

            </div>

            <div class="status-badge">

                <?php echo $booking['payment_status']; ?>

            </div>

        </div>

        <!-- CUSTOMER DETAILS -->

        <h4 class="section-title">
            Customer Details
        </h4>

        <div class="details-grid">

            <div class="detail-card">

                <h6>Customer Name</h6>

                <p>
                    <?php echo $booking['customer_name']; ?>
                </p>

            </div>

            <div class="detail-card">

                <h6>Email Address</h6>

                <p>
                    <?php echo $booking['customer_email']; ?>
                </p>

            </div>

            <div class="detail-card">

                <h6>Phone Number</h6>

                <p>
                    <?php echo $booking['customer_phone']; ?>
                </p>

            </div>
            <div class="detail-card">

                <h6>Business Name</h6>

                <p>
                   <?php echo $business_name; ?>
                </p>

            </div>
            <div class="detail-card">

                <h6>Vendor Type</h6>

                <p>
                   <?php echo $vendor_type; ?>
                </p>

            </div>

        </div>

        <!-- EVENT DETAILS -->

        <h4 class="section-title">
            Event Details
        </h4>

        <div class="details-grid">

            <div class="detail-card">

                <h6>Service Name</h6>

                <p>
                    <?php echo $booking['service_name']; ?>
                </p>

            </div>

            <div class="detail-card">

                <h6>Event Date</h6>

                <p>
                    <?php echo $booking['event_date']; ?>
                </p>

            </div>

            <div class="detail-card">

                <h6>Event Time</h6>

                <p>
                    <?php echo $booking['event_time']; ?>
                </p>

            </div>

            <div class="detail-card">

                <h6>Guest Count</h6>

                <p>
                    <?php echo $booking['guest_count']; ?>
                </p>

            </div>

            <div class="detail-card">

                <h6>Event Location</h6>

                <p>
                    <?php echo $booking['event_location']; ?>
                </p>

            </div>

            <div class="detail-card">

                <h6>Booking Status</h6>

                <p>
                    <?php echo $booking['booking_status']; ?>
                </p>

            </div>

        </div>

        <!-- SERVICE TABLE -->

        <h4 class="section-title">
            Payment Summary
        </h4>

        <table class="invoice-table">

            <thead>

                <tr>

                    <th>Service</th>
                    <th>Event Date</th>
                    <th>Amount</th>
                    <th>Total</th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>
                        <?php echo $booking['service_name']; ?>
                    </td>

                    <td>
                        <?php echo $booking['event_date']; ?>
                    </td>

                    <td>
                        ₹<?php echo number_format($booking['amount'],2); ?>
                    </td>

                    <td class="fw-bold">
                        ₹<?php echo number_format($booking['amount'],2); ?>
                    </td>

                </tr>

            </tbody>

        </table>

        <!-- TOTAL SECTION -->

        <div class="total-section">
            <div class="total-row">

                <span>Final Amount</span>

                <span>
                    ₹<?php echo number_format($booking['amount'],2); ?>
                </span>

            </div>

            <div class="total-row grand-total">

                <span>Total Payable</span>

                <span>
                    ₹<?php echo number_format($booking['amount'],2); ?>
                </span>

            </div>

        </div>

        <!-- PAYMENT BUTTON -->

        <form action="payment_success.php" method="POST">

            <input type="hidden"
            name="booking_id"
            value="<?php echo $booking_id; ?>">

            <button class="pay-btn">

                Pay Now

            </button>

        </form>

        <div class="footer-text">

            Demo Payment Gateway • Event Management System

        </div>

    </div>

</div>

</body>
</html>