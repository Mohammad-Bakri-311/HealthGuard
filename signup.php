<?php
session_start();
include("db_connection.php");

$message = "";

if (isset($_POST['name'])) {
    $conn = OpenCon();

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($name == "" || $email == "" || $password == "" || $confirm_password == "") {
        $message = "Please fill all fields";
    } elseif ($password != $confirm_password) {
        $message = "Passwords do not match";
    } else {
        $check = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $message = "Email already exists";
        } else {
            $role = "patient";

            $sql = "INSERT INTO users (name, role, email, password, created_at)
                    VALUES ('$name', '$role', '$email', '$password', NOW())";

            if (mysqli_query($conn, $sql)) {
                $user_id = mysqli_insert_id($conn);

                mysqli_query($conn, "INSERT INTO patients (user_id, age, gender, medical_notes)
                                     VALUES ('$user_id', NULL, NULL, '')");

                $message = "Signup successful";
            } else {
                $message = "Error: " . mysqli_error($conn);
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
    <title>Sign Up - HealthGuard</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("navbar.php"); ?>

<div class="form-container">
    <form class="form-box" method="POST">
        <h2>Create Account</h2>
        <p class="subtitle">Create your HealthGuard account</p>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter your name">

        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email">

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm password">

        <button type="submit">Sign Up</button>

        <p class="bottom-text">
            Already have an account? <a href="/HealthGuard/login.php">Login</a>
        </p>
    </form>
</div>

</body>
</html>