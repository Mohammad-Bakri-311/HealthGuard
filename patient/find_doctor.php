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

// select all illnesses ordered by name
$sql = "SELECT * FROM illnesses ORDER BY illness_name ASC";

// run query
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Doctor</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="form-container">
    <form class="form-box" method="POST" action="/HealthGuard/patient/show_doctors.php">
        <h2>Find a Doctor</h2>
        <p class="subtitle">Select your illness to find the right doctor</p>

        <div class="hero-actions" style="justify-content:center; margin-bottom:20px;">
            <a href="/HealthGuard/patient/patient_dashboard.php" class="secondary-btn">Back to Dashboard</a>
        </div>

        <label>Select illness</label>
        <select name="illness_id" required>
            <option value="">Choose illness</option>

            <?php 
            // loop through illnesses
            while ($row = mysqli_fetch_assoc($result)) { 
            ?>
                <option value="<?php echo $row['illness_id']; ?>">
                    <?php echo $row['illness_name']; ?>
                </option>
            <?php } ?>

        </select>

        <button type="submit">Search Doctors</button>
    </form>
</div>

</body>
</html>

<?php
// close DB connection
CloseCon($conn);
?>