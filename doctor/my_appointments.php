<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "doctor") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$conn = OpenCon();
$user_id = $_SESSION['user_id'];

$sqlDoctor = "SELECT doctor_id FROM doctors WHERE user_id='$user_id'";
$resultDoctor = mysqli_query($conn, $sqlDoctor);
$doctor = mysqli_fetch_assoc($resultDoctor);
$doctor_id = $doctor['doctor_id'];

$sql = "SELECT appointments.*, users.name AS patient_name, users.email AS patient_email,
               medical_reports.report_id
        FROM appointments
        JOIN users ON appointments.patient_id = users.user_id
        LEFT JOIN medical_reports ON appointments.appointment_id = medical_reports.appointment_id
        WHERE appointments.doctor_id = '$doctor_id'
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
<body class="doctor-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Appointments</h1>
    <p class="section-subtitle">Appointments booked by patients</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/doctor/doctor_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Report</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['patient_email']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <a href="/HealthGuard/doctor/manage_appointment.php?appointment_id=<?php echo $row['appointment_id']; ?>" class="primary-btn">
                        <?php echo $row['report_id'] ? 'Open Report' : 'Start Visit'; ?>
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>
<?php CloseCon($conn); ?>