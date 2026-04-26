<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

if (!isset($_GET['notification_id'])) {
    header("Location: /HealthGuard/patient/my_notifications.php");
    exit();
}

$conn = OpenCon();
$user_id = $_SESSION['user_id'];
$notification_id = $_GET['notification_id'];

mysqli_query($conn, "UPDATE notifications
                     SET status='read'
                     WHERE notification_id='$notification_id' AND user_id='$user_id'");

CloseCon($conn);
header("Location: /HealthGuard/patient/my_notifications.php");
exit();
?>