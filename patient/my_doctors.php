<?php
// start session
session_start();

// include DB connection
include("../db_connection.php");

// check if patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    // redirect to login
    header("Location: /HealthGuard/login.php");
    // stop script
    exit();
}

// open DB connection
$conn = OpenCon();

// select doctors with user data
$sql = "SELECT doctors.doctor_id, users.name, users.email, doctors.specialization
        FROM doctors
        INNER JOIN users ON doctors.user_id = users.user_id
        ORDER BY users.name ASC";

// run query
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Doctors</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Doctors</h1>
    <p class="section-subtitle">Doctors available in the system</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/patient/patient_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Message</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td>Dr. <?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['specialization']; ?></td>
                <td>
                    <a href="/HealthGuard/patient/my_messages.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="primary-btn">Open Chat</a>
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