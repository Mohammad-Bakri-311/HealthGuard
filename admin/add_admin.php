<?php
// start session
session_start();

// include DB connection
include("../db_connection.php");

// check if admin logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: /HealthGuard/login.php");
    exit();
}

// message
$message = "";

// when form submitted
if (isset($_POST['name'])) {

    $conn = OpenCon();

    // get inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $role = "admin";

    // validation
    if ($name == "" || $email == "" || $password == "") {
        $message = "Please fill all fields";
    } else {

        // check if email exists
        $check = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $message = "Email already exists";
        } else {

            // insert admin
            $sql = "INSERT INTO users (name, role, email, password, created_at)
                    VALUES ('$name', '$role', '$email', '$password', NOW())";

            if (mysqli_query($conn, $sql)) {
                $message = "Admin added successfully";
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
    <title>Add Admin</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="form-container">
    <form class="form-box" method="POST">
        <h2>Add Admin</h2>

        <div class="hero-actions" style="justify-content:center; margin-bottom:20px;">
            <a href="/HealthGuard/admin/admin_dashboard.php" class="secondary-btn">Back to Dashboard</a>
        </div>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <label>Name</label>
        <input type="text" name="name" placeholder="Enter admin name">

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter admin email">

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password">

        <button type="submit">Add Admin</button>
    </form>
</div>

</body>
</html>