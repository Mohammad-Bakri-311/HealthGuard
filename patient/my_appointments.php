<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$conn = OpenCon();
$patient_id = $_SESSION['user_id'];

$sql = "SELECT appointments.*, users.name AS doctor_name, users.email AS doctor_email, doctors.specialization
        FROM appointments
        JOIN doctors ON appointments.doctor_id = doctors.doctor_id
        JOIN users ON doctors.user_id = users.user_id
        WHERE appointments.patient_id = '$patient_id'
        ORDER BY appointment_date DESC, appointment_time DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Appointments</h1>
    <p class="section-subtitle">Manage your booked appointments</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/patient/patient_dashboard.php" class="secondary-btn">Back to Dashboard</a>
        <a href="/HealthGuard/patient/find_doctor.php" class="primary-btn">Book New Appointment</a>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>Doctor</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['doctor_email']); ?></td>
                <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <a href="/HealthGuard/patient/delete_appointment.php?appointment_id=<?php echo $row['appointment_id']; ?>"
                       class="danger-btn"
                       onclick="return confirm('Are you sure you want to remove this appointment?');">
                       Remove
                    </a>
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