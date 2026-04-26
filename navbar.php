<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar">
    <div class="nav-left">
        <a href="/HealthGuard/index.php" class="logo">HealthGuard</a>
    </div>

    <div class="nav-right">

        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == "admin") { ?>
            <a href="/HealthGuard/admin/admin_dashboard.php">Dashboard</a>
            <a href="/HealthGuard/admin/manage_users.php">Users</a>
            <a href="/HealthGuard/admin/manage_doctors.php">Doctors</a>
            <a href="/HealthGuard/admin/manage_patients.php">Patients</a>
            <a href="/HealthGuard/admin/manage_drugs.php">Drugs</a>
            <a href="/HealthGuard/logout.php" class="nav-logout">Logout</a>

        <?php } elseif (isset($_SESSION['user_id']) && $_SESSION['role'] == "doctor") { ?>
            <a href="/HealthGuard/doctor/doctor_dashboard.php">Dashboard</a>
            <a href="/HealthGuard/doctor/my_profile.php">Profile</a>
            <a href="/HealthGuard/doctor/my_patients.php">Patients</a>
            <a href="/HealthGuard/doctor/my_messages.php">Messages</a>
            <a href="/HealthGuard/doctor/my_appointments.php">Appointments</a>
            <a href="/HealthGuard/doctor/my_reports.php">Reports</a>
            <a href="/HealthGuard/logout.php" class="nav-logout">Logout</a>

        <?php } elseif (isset($_SESSION['user_id']) && $_SESSION['role'] == "patient") { ?>
            <a href="/HealthGuard/patient/patient_dashboard.php">Dashboard</a>
            <a href="/HealthGuard/patient/my_doctors.php">Doctors</a>
            <a href="/HealthGuard/logout.php" class="nav-logout">Logout</a>

        <?php } else { ?>
            <a href="/HealthGuard/index.php">Home</a>
            <a href="/HealthGuard/signup.php">Sign Up</a>
        <?php } ?>

    </div>
</nav>