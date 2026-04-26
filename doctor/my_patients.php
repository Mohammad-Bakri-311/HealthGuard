<?php
// start session
session_start();

// include DB connection
include("../db_connection.php");

// check if doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "doctor") {
    // redirect to login
    header("Location: /HealthGuard/login.php");
    // stop script
    exit();
}

// open DB connection
$conn = OpenCon();

// select all patients
$sql = "SELECT * FROM users WHERE role='patient' ORDER BY user_id DESC";

// run query
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Patients</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Patients</h1>
    <p class="section-subtitle">Patients available in the system</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/doctor/doctor_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Nutrition</th>
            </tr>

            <?php 
            while ($row = mysqli_fetch_assoc($result)) { 
            ?>
            <tr>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <a href="/HealthGuard/doctor/manage_nutrition.php?patient_id=<?php echo $row['user_id']; ?>" class="primary-btn">Manage Plan</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>

<?php
CloseCon($conn);
?>