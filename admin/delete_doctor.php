<?php
// start session
session_start();

// include DB connection
include("../db_connection.php");

// check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    // redirect to login
    header("Location: /HealthGuard/login.php");
    // stop script
    exit();
}

// check if required GET values exist
if (!isset($_GET['doctor_id']) || !isset($_GET['user_id'])) {
    // redirect if missing data
    header("Location: /HealthGuard/admin/admin_dashboard.php");
    // stop script
    exit();
}

// open DB connection
$conn = OpenCon();

// get ids from URL
$doctor_id = $_GET['doctor_id'];
$user_id = $_GET['user_id'];

// delete doctor from doctors table
mysqli_query($conn, "DELETE FROM doctors WHERE doctor_id='$doctor_id'");

// delete user from users table
mysqli_query($conn, "DELETE FROM users WHERE user_id='$user_id'");

// close connection
CloseCon($conn);

// redirect back to dashboard
header("Location: /HealthGuard/admin/admin_dashboard.php");

// stop script
exit();
?>