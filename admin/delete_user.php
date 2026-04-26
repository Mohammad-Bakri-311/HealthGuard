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

// check if user_id exists in URL
if (!isset($_GET['user_id'])) {
    // redirect if missing
    header("Location: /HealthGuard/admin/manage_users.php");
    // stop script
    exit();
}

// open DB connection
$conn = OpenCon();

// get user id
$user_id = $_GET['user_id'];

// delete user from table
mysqli_query($conn, "DELETE FROM users WHERE user_id='$user_id'");

// close connection
CloseCon($conn);

// redirect back
header("Location: /HealthGuard/admin/manage_users.php");

// stop script
exit();
?>