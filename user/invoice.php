<?php
session_start();

/* ================= DB CONNECTION ================= */
$conn = new mysqli("localhost","root","","event_management_system");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* ================= LOGIN CHECK ================= */
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

/* ================= INVOICE ID CHECK ================= */
if(!isset($_GET['invoice_id'])){
    die("Invoice ID Missing");
}

$invoice_id = $_GET['invoice_id'];
$user_id = $_SESSION['user_id'];

/* ================= FETCH INVOICE ================= */
$invoice_q = mysqli_query($conn,"
SELECT *
FROM invoices
WHERE invoice_id='$invoice_id'
AND user_id='$user_id'
");

$invoice = mysqli_fetch_assoc($invoice_q);

if(!$invoice){
    die("Invoice not found");
}

/* ================= FETCH ITEMS ================= */
$items_q = mysqli_query($conn,"
SELECT *
FROM booking_items
WHERE invoice_id='$invoice_id'
ORDER BY item_id DESC
");

if(!$items_q){
    die("Items Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Invoice</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    background:#f5f7fb;
    font-family:Poppins;
}

.invoice-box{
    background:white;
    border-radius:20px;
    padding:35px;
    box-shadow:0 5px 25px rgba(0,0,0,0.08);
}

.invoice-header{
    border-bottom:2px dashed #ddd;
    padding-bottom:20px;
    margin-bottom:25px;
}

.logo-title{
    font-size:32px;
    font-weight:700;
    color:#6c63ff;
}

.invoice-id{
    font-size:18px;
    font-weight:600;
}

.status-badge{
    background:#fff3cd;
    color:#856404;
    padding:8px 18px;
    border-radius:20px;
    font-size:14px;
    font-weight:600;
}

.table thead{
    background:#6c63ff;
    color:white;
}

.table td,
.table th{
    vertical-align:middle;
}

.total-box{
    background:#f8f9ff;
    border-radius:16px;
    padding:25px;
}

.total-amount{
    font-size:36px;
    font-weight:700;
    color:#28a745;
}

.btn-pay{
    background:#6c63ff;
    color:white;
    border:none;
    padding:12px 30px;
    border-radius:12px;
    font-weight:600;
}

.btn-pay:hover{
    background:#5848e5;
}

.service-badge{
    background:#e9ecff;
    color:#4b44d4;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}
</style>

</head>

<body>

<div class="container py-5">

<div class="invoice-box">

<!-- HEADER -->
<div class="invoice-header d-flex justify-content-between">

    <div>
        <h1 class="logo-title">
            <i class="fa fa-calendar-check me-2"></i>
            EventSpace
        </h1>
        <p class="text-muted mb-0">Professional Event Booking Invoice</p>
    </div>

    <div class="text-end">
        <div class="invoice-id mb-2">
            Invoice #INV-<?php echo $invoice['invoice_id']; ?>
        </div>
        <span class="status-badge">
            <?php echo $invoice['payment_status']; ?>
        </span>
    </div>

</div>

<!-- TABLE -->
<h4 class="mb-4 fw-bold">Booked Services</h4>

<div class="table-responsive">
<table class="table table-bordered">

<thead>
<tr>
    <th>#</th>
    <th>Service</th>
    <th>Type</th>
    <th>Quantity</th>
    <th>Price</th>
    <th>Subtotal</th>
</tr>
</thead>

<tbody>

<?php
$count = 1;
$total_payable = 0;

while($item = mysqli_fetch_assoc($items_q)){

    $subtotal = $item['price'] * $item['quantity'];
    $total_payable += $subtotal;
?>

<tr>

    <td><?php echo $count++; ?></td>

    <td><strong><?php echo $item['service_name']; ?></strong></td>

    <td>
        <span class="service-badge">
            <?php echo ucfirst($item['service_type']); ?>
        </span>
    </td>

    <td><?php echo $item['quantity']; ?></td>

    <td>₹<?php echo number_format($item['price']); ?></td>

    <td class="fw-bold text-success">
        ₹<?php echo number_format($subtotal); ?>
    </td>

</tr>

<?php } ?>

</tbody>
</table>
</div>

<!-- TOTAL -->
<div class="row mt-4">

<div class="col-md-6">
    <div class="total-box">

        <h5>Payment Info</h5>

        <p>Status: <strong><?php echo $invoice['payment_status']; ?></strong></p>

        <p>Date: <strong>
            <?php echo date("d M Y", strtotime($invoice['created_at'])); ?>
        </strong></p>

    </div>
</div>

<div class="col-md-6">
    <div class="total-box text-end">

        <h5>Total Payable</h5>

        <div class="total-amount">
            ₹<?php echo number_format($total_payable); ?>
        </div>

        <button class="btn btn-pay mt-3">
            Pay Now
        </button>

    </div>
</div>

</div>

</div>
</div>

</body>
</html>