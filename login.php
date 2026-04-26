<?php
// start session
session_start();

// include DB connection
include("db_connection.php");

// message variable
$message = "";

// check form submit
if (isset($_POST['email'])) {

    // open DB connection
    $conn = OpenCon();

    // get inputs
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // check empty fields
    if ($email == "" || $password == "") {
        // set message
        $message = "Please fill all fields";
    } else {

        // select user by email
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        // if user found
        if (mysqli_num_rows($result) == 1) {

            // get user data
            $row = mysqli_fetch_assoc($result);

            // check password
            if ($password == $row['password']) {

                // store session data
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['email'] = $row['email'];

                // redirect based on role
                if ($row['role'] == "admin") {
                    header("Location: /HealthGuard/admin/admin_dashboard.php");
                    exit();
                } elseif ($row['role'] == "doctor") {
                    header("Location: /HealthGuard/doctor/doctor_dashboard.php");
                    exit();
                } elseif ($row['role'] == "patient") {
                    header("Location: /HealthGuard/patient/patient_dashboard.php");
                    exit();
                } else {
                    header("Location: /HealthGuard/index.php");
                    exit();
                }

            } else {
                // wrong password
                $message = "Wrong password";
            }

        } else {
            // email not found
            $message = "Email not found";
        }
    }

    // close DB connection
    CloseCon($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HealthGuard</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("navbar.php"); ?>

<div class="form-container">
    <form class="form-box" method="POST">
        <h2>Login</h2>
        <p class="subtitle">Login to your HealthGuard account</p>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email">

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password">

        <button type="submit">Login</button>

        <p class="bottom-text">
            Don’t have an account? <a href="/HealthGuard/signup.php">Sign Up</a>
        </p>
    </form>
</div>

</body>
</html>