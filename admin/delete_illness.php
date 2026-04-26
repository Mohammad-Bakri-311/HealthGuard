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

// check if illness_id exists in URL
if (!isset($_GET['illness_id'])) {
    // redirect if missing
    header("Location: /HealthGuard/admin/manage_illnesses.php");
    // stop script
    exit();
}

// open DB connection
$conn = OpenCon();

// get illness id
$illness_id = $_GET['illness_id'];

// delete illness from table
mysqli_query($conn, "DELETE FROM illnesses WHERE illness_id='$illness_id'");

// close connection
CloseCon($conn);

// redirect back
header("Location: /HealthGuard/admin/manage_illnesses.php");

// stop script
exit();
?>