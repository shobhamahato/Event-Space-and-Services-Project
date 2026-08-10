<?php

session_start();

$conn = new mysqli("localhost","root","","event_management_system");

$user_id = $_SESSION['user_id'];

$vendor_id = $_POST['vendor_id'];

$event_date = $_POST['event_date'];

$event_time = $_POST['event_time'];

$event_location = $_POST['event_location'];

$guest_count = $_POST['guest_count'];

$special_request = $_POST['special_request'];

$amount = $_POST['amount'];

$sql = "INSERT INTO bookings(

user_id,
vendor_id,
event_date,
event_time,
event_location,
guest_count,
special_request,
amount

)

VALUES(

'$user_id',
'$vendor_id',
'$event_date',
'$event_time',
'$event_location',
'$guest_count',
'$special_request',
'$amount'

)";

mysqli_query($conn,$sql);

$booking_id = mysqli_insert_id($conn);

header("Location: payment.php?booking_id=$booking_id");

?>