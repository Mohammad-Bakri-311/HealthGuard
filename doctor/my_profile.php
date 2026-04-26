<?php
// start session
session_start();

// include DB connection
include("../db_connection.php");

// check if doctor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "doctor") {
    header("Location: /HealthGuard/login.php");
    exit();
}

// open connection
$conn = OpenCon();

// logged in doctor user id
$user_id = $_SESSION['user_id'];

// get doctor info with user info
$sql = "SELECT users.name, users.email, doctors.specialization, doctors.license_number
        FROM users
        INNER JOIN doctors ON users.user_id = doctors.user_id
        WHERE users.user_id = '$user_id' AND users.role = 'doctor'";

$result = mysqli_query($conn, $sql);
$doctor = mysqli_fetch_assoc($result);

// safe values
$doctor_name = $doctor['name'] ?? '';
$doctor_email = $doctor['email'] ?? '';
$doctor_specialization = $doctor['specialization'] ?? '';
$doctor_license = $doctor['license_number'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="doctor-page">

<?php include("../navbar.php"); ?>

<div class="form-container">
    <div class="form-box">
        <h2>My Profile</h2>
        <p class="subtitle">Doctor account information</p>

        <div class="hero-actions" style="justify-content:center; margin-bottom:20px;">
            <a href="/HealthGuard/doctor/doctor_dashboard.php" class="secondary-btn">Back to Dashboard</a>
        </div>

        <?php if ($doctor) { ?>

            <label>Doctor Name</label>
            <input type="text" value="<?php echo htmlspecialchars($doctor_name); ?>" readonly>

            <label>Email</label>
            <input type="text" value="<?php echo htmlspecialchars($doctor_email); ?>" readonly>

            <label>Specialization</label>
            <input type="text" value="<?php echo htmlspecialchars($doctor_specialization); ?>" readonly>

            <label>License Number</label>
            <input type="text" value="<?php echo htmlspecialchars($doctor_license); ?>" readonly>

        <?php } else { ?>

            <div class="message">Doctor profile not found.</div>

        <?php } ?>
    </div>
</div>

</body>
</html>

<?php
// close connection
CloseCon($conn);
?>