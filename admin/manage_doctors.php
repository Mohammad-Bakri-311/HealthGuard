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

// open DB connection
$conn = OpenCon();

// select doctors with user data using JOIN
$sql = "SELECT doctors.doctor_id, users.user_id, users.name, users.email, doctors.specialization
        FROM doctors
        INNER JOIN users ON doctors.user_id = users.user_id
        ORDER BY doctors.doctor_id DESC";

// run query
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">Manage Doctors</h1>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/admin/add_doctor.php" class="primary-btn">Add Doctor</a>
        <a href="/HealthGuard/admin/admin_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>Doctor ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Action</th>
            </tr>

            <?php 
            // loop through results
            while ($row = mysqli_fetch_assoc($result)) { 
            ?>
            <tr>
                <td><?php echo $row['doctor_id']; ?></td>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['specialization']; ?></td>
                <td>
                    <a href="/HealthGuard/admin/delete_doctor.php?doctor_id=<?php echo $row['doctor_id']; ?>&user_id=<?php echo $row['user_id']; ?>" class="danger-btn">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>

<?php
// close DB connection
CloseCon($conn);
?>