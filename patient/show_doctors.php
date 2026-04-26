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

// initialize variables
$illness_id = "";
$specialization = "";
$illness_name = "";

// get illness_id from POST
if (isset($_POST['illness_id'])) {
    $illness_id = $_POST['illness_id'];
}

// get illness info
$sqlIllness = "SELECT * FROM illnesses WHERE illness_id='$illness_id'";
$resultIllness = mysqli_query($conn, $sqlIllness);
$illness = mysqli_fetch_assoc($resultIllness);

// if illness found
if ($illness) {
    // get specialization and illness name
    $specialization = $illness['specialization'];
    $illness_name = $illness['illness_name'];
}

// select doctors by specialization
$sql = "SELECT doctors.doctor_id, users.name, users.email, doctors.specialization
        FROM doctors
        JOIN users ON doctors.user_id = users.user_id
        WHERE doctors.specialization = '$specialization'";

// run query
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">Available Doctors</h1>
    <p class="section-subtitle">Doctors for: <?php echo $illness_name; ?></p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/patient/patient_dashboard.php" class="secondary-btn">Back to Dashboard</a>
        <a href="/HealthGuard/patient/find_doctor.php" class="primary-btn">Search Again</a>
    </div>

    <div class="cards">

        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="card">
                    <h3>Dr. <?php echo $row['name']; ?></h3>
                    <p><strong>Specialization:</strong> <?php echo $row['specialization']; ?></p>
                    <p><strong>Email:</strong> <?php echo $row['email']; ?></p>

                    <div class="hero-actions" style="justify-content:center; margin-top:15px;">
                        <a href="/HealthGuard/patient/book_appointment.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="primary-btn">Book Appointment</a>
                        <a href="/HealthGuard/patient/my_messages.php?doctor_id=<?php echo $row['doctor_id']; ?>" class="secondary-btn">Message Doctor</a>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="card">
                <h3>No doctor found</h3>
                <p>No doctor is available for this illness right now.</p>
            </div>
        <?php } ?>

    </div>
</div>

</body>
</html>
<?php
// close DB connection
CloseCon($conn);
?>