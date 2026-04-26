<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

if (!isset($_GET['doctor_id'])) {
    header("Location: /HealthGuard/patient/find_doctor.php");
    exit();
}

$conn = OpenCon();
$doctor_id = $_GET['doctor_id'];
$message = "";

$sqlDoctor = "SELECT doctors.doctor_id, users.name, users.email, doctors.specialization
              FROM doctors
              JOIN users ON doctors.user_id = users.user_id
              WHERE doctors.doctor_id = '$doctor_id'";
$resultDoctor = mysqli_query($conn, $sqlDoctor);
$doctor = mysqli_fetch_assoc($resultDoctor);

if (isset($_POST['appointment_date'])) {
    $patient_id = $_SESSION['user_id'];
    $appointment_date = trim($_POST['appointment_date']);
    $appointment_time = trim($_POST['appointment_time']);

    if ($appointment_date == "" || $appointment_time == "") {
        $message = "Please fill all fields";
    } else {
        $check = "SELECT * FROM appointments
                  WHERE doctor_id='$doctor_id'
                  AND appointment_date='$appointment_date'
                  AND appointment_time='$appointment_time'";
        $resultCheck = mysqli_query($conn, $check);

        if (mysqli_num_rows($resultCheck) > 0) {
            $message = "This time is already booked. Choose another time.";
        } else {
            $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status)
                    VALUES ('$patient_id', '$doctor_id', '$appointment_date', '$appointment_time', 'pending')";

            if (mysqli_query($conn, $sql)) {
                $sqlDoctorUser = "SELECT user_id FROM doctors WHERE doctor_id='$doctor_id'";
                $resultDoctorUser = mysqli_query($conn, $sqlDoctorUser);
                $rowDoctorUser = mysqli_fetch_assoc($resultDoctorUser);
                $doctor_user_id = $rowDoctorUser['user_id'];

                mysqli_query($conn, "INSERT INTO notifications (user_id, type, created_at, message, status)
                                     VALUES ('$doctor_user_id', 'appointment', NOW(), 'A new appointment has been booked by a patient.', 'unread')");

                mysqli_query($conn, "INSERT INTO notifications (user_id, type, created_at, message, status)
                                     VALUES ('$patient_id', 'appointment', NOW(), 'Your appointment has been booked successfully.', 'unread')");

                $message = "Appointment booked successfully";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="booking-shell">
    <div class="booking-left">
        <span class="patient-badge">Appointment</span>
        <h1>Book an Appointment</h1>
        <p>
            Select a date and time that works for you.
            If the selected time is already taken, choose another one.
        </p>

        <div class="booking-help">
            <h3>Before booking</h3>
            <p>Check the doctor information carefully, then choose the right date and time.</p>
        </div>
    </div>

    <div class="booking-right">
        <form class="form-box booking-form" method="POST">
            <h2>Appointment Details</h2>
            <p class="subtitle">Complete your booking request</p>

            <div class="hero-actions" style="justify-content:center; margin-bottom:20px;">
                <a href="/HealthGuard/patient/find_doctor.php" class="secondary-btn">Back</a>
                <a href="/HealthGuard/patient/my_appointments.php" class="secondary-btn">My Appointments</a>
            </div>

            <?php if (!empty($message)) { ?>
                <div class="message"><?php echo $message; ?></div>
            <?php } ?>

            <?php if ($doctor) { ?>
                <label>Doctor Name</label>
                <input type="text" value="Dr. <?php echo htmlspecialchars($doctor['name']); ?>" readonly>

                <label>Email</label>
                <input type="text" value="<?php echo htmlspecialchars($doctor['email']); ?>" readonly>

                <label>Specialization</label>
                <input type="text" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" readonly>

                <label>Appointment Date</label>
                <input type="date" name="appointment_date">

                <label>Appointment Time</label>
                <input type="time" name="appointment_time">

                <button type="submit">Book Appointment</button>
            <?php } else { ?>
                <div class="message">Doctor not found.</div>
            <?php } ?>
        </form>
    </div>
</div>

</body>
</html>
<?php
CloseCon($conn);
?>