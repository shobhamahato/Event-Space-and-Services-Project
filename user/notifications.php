<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/db.php');

$user_id = $_SESSION['user_id'];

include('../includes/user-navbar.php');

/*
====================================================
MARK NOTIFICATIONS AS READ
====================================================
*/

$update = "
UPDATE notifications 
SET is_read = 1 
WHERE user_id = ? AND is_read = 0
";

$stmt = $conn->prepare($update);
$stmt->bind_param("i", $user_id);
$stmt->execute();

/*
====================================================
FETCH NOTIFICATIONS
====================================================
*/

$query = "
SELECT *
FROM notifications
WHERE user_id = ?
ORDER BY created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>

<title>Notifications</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

body{
    background:#f5f7fb;
    font-family:Arial;
}

.notification-container{
    max-width:900px;
    margin:40px auto;
}

.notification-card{
    background:#fff;
    border-radius:15px;
    padding:20px;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
    transition:0.3s;
}

.notification-card.read{
    opacity:0.6;
    background:#f1f1f1;
}

.notification-content{
    flex:1;
}

.notification-title{
    font-size:18px;
    font-weight:bold;
}

.notification-message{
    color:#555;
    margin-top:5px;
}

.notification-date{
    color:gray;
    font-size:13px;
    margin-top:5px;
}

.badge-read{
    background:#6c757d;
    color:white;
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
}

.badge-new{
    background:#dc3545;
    color:white;
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
}

</style>

</head>

<body>

<div class="container notification-container">

<h2 class="mb-4">
    <i class="fa-solid fa-bell"></i>
    Notifications
</h2>

<?php if($result->num_rows > 0){ ?>

    <?php while($row = $result->fetch_assoc()){ ?>

        <div class="notification-card <?php echo ($row['is_read'] == 1) ? 'read' : ''; ?>">

            <div class="notification-content">

                <div class="notification-title">
                    <?php echo $row['title']; ?>
                </div>

                <div class="notification-message">
                    <?php echo $row['message']; ?>
                </div>

                <div class="notification-date">
                    <?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?>
                </div>

            </div>

            <div>
                <?php if($row['is_read'] == 1){ ?>

                    <span class="badge-read">
                        Read
                    </span>

                <?php } else { ?>

                    <span class="badge-new">
                        New
                    </span>

                <?php } ?>
            </div>

        </div>

    <?php } ?>

<?php } else { ?>

    <div class="alert alert-info">
        No notifications found.
    </div>

<?php } ?>

</div>

</body>
</html>