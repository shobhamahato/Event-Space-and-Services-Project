<?php
session_start();

$conn = new mysqli("localhost","root","","event_management_system");

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ================= STEP 1 DATA ================= */

if(!isset($_SESSION['book_all_step1'])){
    die("Step 1 data missing");
}

$step1 = $_SESSION['book_all_step1'];

/* ================= COMMON EVENT DETAILS ================= */

$customer_name   = mysqli_real_escape_string($conn,$step1['customer_name']);
$customer_email  = mysqli_real_escape_string($conn,$step1['customer_email']);
$customer_phone  = mysqli_real_escape_string($conn,$step1['customer_phone']);

$event_date      = mysqli_real_escape_string($conn,$step1['event_date']);
$event_time      = mysqli_real_escape_string($conn,$step1['event_time']);
$event_location  = mysqli_real_escape_string($conn,$step1['event_location']);
$guest_count     = mysqli_real_escape_string($conn,$step1['guest_count']);

/* ================= SELECTED SERVICES ================= */

$selected_services = [];

/* DECORATOR */
if(isset($_POST['decorator_enabled'])){

    $selected_services['decorator'] = json_encode($_POST['decorator']);
}

/* CATERER */
if(isset($_POST['caterer_enabled'])){

    $selected_services['caterer'] = json_encode($_POST['caterer']);
}

/* VENUE */
if(isset($_POST['venue_enabled'])){

    $selected_services['venue'] = json_encode($_POST['venue']);
}

/* MUSIC */
if(isset($_POST['music_enabled'])){

    $selected_services['music'] = json_encode($_POST['music']);
}

/* PHOTOGRAPHY */
if(isset($_POST['photography_enabled'])){

    $selected_services['photography'] = json_encode($_POST['photography']);
}

/* BEAUTY */
if(isset($_POST['beauty_enabled'])){

    $selected_services['beauty'] = json_encode($_POST['beauty']);
}

/* CARDS */
if(isset($_POST['cards_enabled'])){

    $selected_services['cards'] = json_encode($_POST['cards']);
}

/* ================= GET CART VENDORS ================= */

$cart_q = mysqli_query($conn,"
SELECT cart.*, vendors.vendor_type
FROM cart
JOIN vendors 
ON cart.vendor_id = vendors.vendor_id
WHERE cart.user_id='$user_id'
");

while($cart = mysqli_fetch_assoc($cart_q)){

    $vendor_id   = $cart['vendor_id'];
    $vendor_type = strtolower($cart['vendor_type']);

    /* ================= MATCH SELECTED SERVICES ================= */

    if(array_key_exists($vendor_type,$selected_services)){

        $service_details = mysqli_real_escape_string(
            $conn,
            $selected_services[$vendor_type]
        );

        /* ================= INSERT BOOKING ================= */

        mysqli_query($conn,"
        INSERT INTO bookings
        (
            user_id,
            vendor_id,
            vendor_type,
            customer_name,
            customer_email,
            customer_phone,
            event_date,
            event_time,
            event_location,
            guest_count,
            service_details,
            booking_status,
            created_at
        )
        VALUES
        (
            '$user_id',
            '$vendor_id',
            '$vendor_type',
            '$customer_name',
            '$customer_email',
            '$customer_phone',
            '$event_date',
            '$event_time',
            '$event_location',
            '$guest_count',
            '$service_details',
            'Pending',
            NOW()
        )
        ");
    }
}

/* ================= CLEAR SESSION ================= */

unset($_SESSION['book_all_step1']);

/* ================= SUCCESS ================= */

echo "
<script>
alert('Booking requests sent successfully to selected vendors!');
window.location.href='orders.php';
</script>
";
?>