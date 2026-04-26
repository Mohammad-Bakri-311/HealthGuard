<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

if (!isset($_GET['appointment_id'])) {
    header("Location: /HealthGuard/patient/my_appointments.php");
    exit();
}

$conn = OpenCon();

$appointment_id = $_GET['appointment_id'];
$patient_id = $_SESSION['user_id'];

// delete only if this appointment belongs to the logged-in patient
$sql = "DELETE FROM appointments
        WHERE appointment_id = '$appointment_id'
        AND patient_id = '$patient_id'";

mysqli_query($conn, $sql);

CloseCon($conn);

header("Location: /HealthGuard/patient/my_appointments.php");
exit();
?>