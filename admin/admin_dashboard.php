<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$admin_name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HealthGuard</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="hg-patient-dashboard">

    <section class="hg-patient-header">
        <div class="hg-patient-header-text">
            <h1>Welcome Admin, <?php echo htmlspecialchars($admin_name); ?></h1>
            <p>
                Manage the HealthGuard system efficiently. Control users, doctors, patients,
                illnesses, and medications from one clean dashboard.
            </p>
        </div>

        <div class="hg-patient-user-box">
            <div class="hg-patient-avatar">
                <?php echo strtoupper(substr($admin_name, 0, 1)); ?>
            </div>
            <div class="hg-patient-user-info">
                <strong>Admin Panel</strong>
                <span><?php echo htmlspecialchars($admin_name); ?></span>
            </div>
        </div>
    </section>

    <div class="hg-patient-layout">

        <main class="hg-patient-main">

            <section class="hg-panel">
                <div class="hg-panel-head">
                    <h2>System Management</h2>
                    <p>Quick access to system control tools</p>
                </div>

                <div class="hg-service-grid">

                    <a href="/HealthGuard/admin/manage_users.php" class="hg-service-card">
                        <div class="hg-service-icon">👥</div>
                        <span>Users</span>
                        <p class="hg-service-desc">View and manage all system users</p>
                    </a>

                    <a href="/HealthGuard/admin/manage_doctors.php" class="hg-service-card">
                        <div class="hg-service-icon">👨‍⚕️</div>
                        <span>Doctors</span>
                        <p class="hg-service-desc">Add, view, and remove doctors</p>
                    </a>

                    <a href="/HealthGuard/admin/manage_patients.php" class="hg-service-card">
                        <div class="hg-service-icon">🧑</div>
                        <span>Patients</span>
                        <p class="hg-service-desc">Add, view, and remove patients</p>
                    </a>

                    <a href="/HealthGuard/admin/manage_illnesses.php" class="hg-service-card">
                        <div class="hg-service-icon">🦠</div>
                        <span>Illnesses</span>
                        <p class="hg-service-desc">Add and organize illness categories</p>
                    </a>

                    <a href="/HealthGuard/admin/manage_drugs.php" class="hg-service-card">
                        <div class="hg-service-icon">💊</div>
                        <span>Drugs</span>
                        <p class="hg-service-desc">Manage medicines and prescription data</p>
                    </a>

                    <a href="/HealthGuard/admin/add_admin.php" class="hg-service-card">
                        <div class="hg-service-icon">➕</div>
                        <span>Add Admin</span>
                        <p class="hg-service-desc">Create new admin accounts securely</p>
                    </a>

                </div>
            </section>

        </main>

        <aside class="hg-side-menu">
            <div class="hg-side-title">Admin Tips</div>

            <div class="hg-info-box">
                <h4>System Control</h4>
                <p>Keep users, doctors, and patients data organized.</p>
            </div>

            <div class="hg-info-box">
                <h4>Security</h4>
                <p>Only trusted admins should have access to management tools.</p>
            </div>

            <div class="hg-info-box">
                <h4>Health Data</h4>
                <p>Make sure illnesses and drugs are updated correctly.</p>
            </div>
        </aside>

    </div>
</div>

</body>
</html>