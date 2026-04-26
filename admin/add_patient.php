<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$message = "";

if (isset($_POST['name'])) {

    $conn = OpenCon();

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $medical_notes = trim($_POST['medical_notes']);

    $role = "patient";

    if ($name == "" || $email == "" || $password == "" || $age == "" || $gender == "") {
        $message = "Please fill all required fields";
    } else {

        $check = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $message = "Email already exists";
        } else {

            $sql = "INSERT INTO users (name, role, email, password, created_at)
                    VALUES ('$name', '$role', '$email', '$password', NOW())";

            if (mysqli_query($conn, $sql)) {

                $user_id = mysqli_insert_id($conn);

                $sql2 = "INSERT INTO patients (user_id, age, gender, medical_notes)
                         VALUES ('$user_id', '$age', '$gender', '$medical_notes')";

                if (mysqli_query($conn, $sql2)) {
                    $message = "Patient added successfully";
                } else {
                    mysqli_query($conn, "DELETE FROM users WHERE user_id='$user_id'");
                    $message = "Patient table insert failed: " . mysqli_error($conn);
                }

            } else {
                $message = "User insert failed: " . mysqli_error($conn);
            }
        }
    }

    CloseCon($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="form-container">
    <form class="form-box" method="POST">
        <h2>Add Patient</h2>

        <div class="hero-actions" style="justify-content:center; margin-bottom:20px;">
            <a href="/HealthGuard/admin/admin_dashboard.php" class="secondary-btn">Back to Dashboard</a>
        </div>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <label>Name</label>
        <input type="text" name="name" placeholder="Enter patient name">

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter patient email">

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password">

        <label>Age</label>
        <input type="number" name="age" placeholder="Enter age">

        <label>Gender</label>
        <select name="gender">
            <option value="">Select gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>

        <label>Medical Notes</label>
        <textarea name="medical_notes" placeholder="Enter medical notes"></textarea>

        <button type="submit">Add Patient</button>
    </form>
</div>

</body>
</html>