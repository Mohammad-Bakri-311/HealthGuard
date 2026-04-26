<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "doctor") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$doctor_name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - HealthGuard</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="hg-patient-dashboard">

    <section class="hg-patient-header">
        <div class="hg-patient-header-text">
            <h1>Welcome Dr. <?php echo htmlspecialchars($doctor_name); ?></h1>
            <p>
                Manage your patients, appointments, and medical reports easily
                from your personalized dashboard.
            </p>
        </div>

        <div class="hg-patient-user-box">
            <div class="hg-patient-avatar">
                <?php echo strtoupper(substr($doctor_name, 0, 1)); ?>
            </div>
            <div class="hg-patient-user-info">
                <strong>Doctor Panel</strong>
                <span><?php echo htmlspecialchars($doctor_name); ?></span>
            </div>
        </div>
    </section>

    <div class="hg-patient-layout">

        <main class="hg-patient-main">

            <section class="hg-panel">
                <div class="hg-panel-head">
                    <h2>Medical Services</h2>
                    <p>Quick access to your work tools</p>
                </div>

                <div class="hg-service-grid">

                    <a href="/HealthGuard/doctor/my_profile.php" class="hg-service-card">
                        <div class="hg-service-icon">👤</div>
                        <span>My Profile</span>
                    </a>

                    <a href="/HealthGuard/doctor/my_patients.php" class="hg-service-card">
                        <div class="hg-service-icon">🧑</div>
                        <span>My Patients</span>
                    </a>

                    <a href="/HealthGuard/doctor/my_appointments.php" class="hg-service-card">
                        <div class="hg-service-icon">📅</div>
                        <span>Appointments</span>
                    </a>

                    <a href="/HealthGuard/doctor/my_reports.php" class="hg-service-card">
                        <div class="hg-service-icon">📄</div>
                        <span>Reports</span>
                    </a>

                    <a href="/HealthGuard/doctor/my_messages.php" class="hg-service-card">
                        <div class="hg-service-icon">💬</div>
                        <span>Messages</span>
                    </a>

                </div>
            </section>

        </main>

        <aside class="hg-side-menu">
            <div class="hg-side-title">Doctor Tips</div>

            <div class="hg-info-box">
                <h4>Patient Care</h4>
                <p>Always review patient history before prescribing.</p>
            </div>

            <div class="hg-info-box">
                <h4>Appointments</h4>
                <p>Keep your schedule updated and organized.</p>
            </div>

        </aside>

    </div>
</div>

</body>
</html>