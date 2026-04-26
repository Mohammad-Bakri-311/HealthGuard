<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: /HealthGuard/login.php");
    exit();
}

if (!isset($_GET['drug_id'])) {
    header("Location: /HealthGuard/admin/manage_drugs.php");
    exit();
}

$conn = OpenCon();
$drug_id = $_GET['drug_id'];

mysqli_query($conn, "DELETE FROM drugs WHERE drug_id='$drug_id'");

CloseCon($conn);

header("Location: /HealthGuard/admin/manage_drugs.php");
exit();
?>