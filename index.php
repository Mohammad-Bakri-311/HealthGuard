<?php
// start session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthGuard</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php
// include navbar
include("navbar.php");
?>

<div class="hero">
    <div class="hero-box">
        <h1>Welcome to HealthGuard</h1>

        <p>
            HealthGuard is a smart healthcare system that helps patients find the right doctor
            based on their illness. You can create an account, log in, search for doctors,
            and explore the healthcare services available in the system.
        </p>

        <div class="hero-actions">
            <a href="/HealthGuard/patient/find_doctor.php" class="primary-btn">Find a Doctor</a>
            <a href="/HealthGuard/signup.php" class="secondary-btn">Create Account</a>
        </div>
    </div>
</div>

</body>
</html>