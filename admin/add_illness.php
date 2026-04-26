<?php
// show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// message variable
$message = "";

// check form submit
if (isset($_POST['illness_name'])) {

    // open DB connection
    $conn = OpenCon();

    // get inputs
    $illness_name = trim($_POST['illness_name']);
    $specialization = trim($_POST['specialization']);

    // check empty fields
    if ($illness_name == "" || $specialization == "") {
        // set error message
        $message = "Please fill all fields";
    } else {

        // check if illness exists
        $check = "SELECT * FROM illnesses WHERE illness_name='$illness_name'";
        $result = mysqli_query($conn, $check);

        // if exists
        if (mysqli_num_rows($result) > 0) {
            // set message
            $message = "Illness already exists";
        } else {

            // insert illness
            $sql = "INSERT INTO illnesses (illness_name, specialization)
                    VALUES ('$illness_name', '$specialization')";

            // run insert
            if (mysqli_query($conn, $sql)) {
                // success message
                $message = "Illness added successfully";
            } else {
                // error message
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }

    // close connection
    CloseCon($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Illness</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="form-container">
    <form class="form-box" method="POST">
        <h2>Add Illness</h2>
        <p class="subtitle">Create a new illness and connect it to a specialization</p>

        <div class="hero-actions" style="justify-content:center; margin-bottom:20px;">
            <a href="/HealthGuard/admin/manage_illnesses.php" class="secondary-btn">Back</a>
        </div>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <label>Illness Name</label>
        <input type="text" name="illness_name" placeholder="Enter illness name">

        <label>Specialization</label>
        <select name="specialization">
            <option value="">Choose specialization</option>
            <option value="cardiologist">Cardiologist</option>
            <option value="dermatologist">Dermatologist</option>
            <option value="ophthalmologist">Ophthalmologist</option>
            <option value="orthopedic">Orthopedic</option>
            <option value="dentist">Dentist</option>
        </select>

        <button type="submit">Add Illness</button>
    </form>
</div>

</body>
</html>