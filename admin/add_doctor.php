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

// message variable
$message = "";

// check form submit
if (isset($_POST['name'])) {

    // open DB connection
    $conn = OpenCon();

    // get inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $specialization = trim($_POST['specialization']);

    // set role
    $role = "doctor";

    // check empty fields
    if ($name == "" || $email == "" || $password == "" || $specialization == "") {
        // set error message
        $message = "Please fill all fields";
    } else {

        // check if email exists
        $check = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check);

        // if exists
        if (mysqli_num_rows($result) > 0) {
            // set message
            $message = "Email already exists";
        } else {

            // insert into users
            $sqlUser = "INSERT INTO users (name, role, email, password, created_at)
                        VALUES ('$name', '$role', '$email', '$password', NOW())";

            // run user insert
            if (mysqli_query($conn, $sqlUser)) {

                // get user id
                $user_id = mysqli_insert_id($conn);

                // insert into doctors
                $sqlDoctor = "INSERT INTO doctors (user_id, specialization)
                              VALUES ('$user_id', '$specialization')";

                // run doctor insert
                if (mysqli_query($conn, $sqlDoctor)) {
                    // success message
                    $message = "Doctor added successfully";
                } else {
                    // doctor error
                    $message = "Doctor insert error: " . mysqli_error($conn);
                }

            } else {
                // user error
                $message = "User insert error: " . mysqli_error($conn);
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
    <title>Add Doctor</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="form-container">
    <form class="form-box" method="POST">
        <h2>Add Doctor</h2>
        <p class="subtitle">Create a doctor account</p>

        <div class="hero-actions" style="justify-content:center; margin-bottom:20px;">
            <a href="/HealthGuard/admin/admin_dashboard.php" class="secondary-btn">Back to Dashboard</a>
        </div>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <label>Doctor Name</label>
        <input type="text" name="name" placeholder="Enter doctor name">

        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter doctor email">

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password">

        <label>Specialization</label>
        <select name="specialization">
            <option value="">Choose specialization</option>
            <option value="cardiologist">Cardiologist</option>
            <option value="dermatologist">Dermatologist</option>
            <option value="ophthalmologist">Ophthalmologist</option>
            <option value="orthopedic">Orthopedic</option>
            <option value="dentist">Dentist</option>
        </select>

        <button type="submit">Add Doctor</button>
    </form>
</div>

</body>
</html>