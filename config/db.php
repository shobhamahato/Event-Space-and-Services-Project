<?php

$servername = "localhost";
$username   = "root";        // change if different
$password   = "";            // change if you have mysql password
$dbname     = "event_management_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Optional: Set charset to avoid special character issues
$conn->set_charset("utf8");

?>