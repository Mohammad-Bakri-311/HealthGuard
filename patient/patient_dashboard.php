<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$patient_name = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - HealthGuard</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="hg-patient-dashboard">

    <section class="hg-patient-header">
        <div class="hg-patient-header-text">
            <h1>Welcome back, <?php echo htmlspecialchars($patient_name); ?></h1>
            <p>
                HealthGuard helps you manage your healthcare journey in one place.
                Search for the right doctor, book appointments, send messages,
                view reports, and follow your weekly nutrition plan created by your doctor.
            </p>
        </div>

        <div class="hg-patient-user-box">
            <div class="hg-patient-avatar">
                <?php echo strtoupper(substr($patient_name, 0, 1)); ?>
            </div>
            <div class="hg-patient-user-info">
                <strong>Patient Dashboard</strong>
                <span><?php echo htmlspecialchars($patient_name); ?></span>
            </div>
        </div>
    </section>

    <div class="hg-patient-layout">

        <main class="hg-patient-main">

            <section class="hg-panel">
                <div class="hg-panel-head">
                    <h2>Quick Services</h2>
                    <p>Fast access to the main actions in your account</p>
                </div>

                <div class="hg-service-grid">
                    <a href="/HealthGuard/patient/find_doctor.php" class="hg-service-card">
                        <div class="hg-service-icon">👨‍⚕️</div>
                        <span>Find Doctor</span>
                    </a>

                    <a href="/HealthGuard/patient/my_appointments.php" class="hg-service-card">
                        <div class="hg-service-icon">📅</div>
                        <span>Appointments</span>
                    </a>

                    <a href="/HealthGuard/patient/my_messages.php" class="hg-service-card">
                        <div class="hg-service-icon">💬</div>
                        <span>Messages</span>
                    </a>

                    <a href="/HealthGuard/patient/my_doctors.php" class="hg-service-card">
                        <div class="hg-service-icon">🩺</div>
                        <span>My Doctors</span>
                    </a>

                    <a href="/HealthGuard/patient/my_profile.php" class="hg-service-card">
                        <div class="hg-service-icon">👤</div>
                        <span>My Profile</span>
                    </a>

                    <a href="/HealthGuard/patient/my_reports.php" class="hg-service-card">
                        <div class="hg-service-icon">📄</div>
                        <span>Reports</span>
                    </a>

                    <a href="/HealthGuard/patient/my_allergies.php" class="hg-service-card">
                        <div class="hg-service-icon">⚠️</div>
                        <span>Allergies</span>
                    </a>

                    <a href="/HealthGuard/patient/my_nutrition_plan.php" class="hg-service-card">
                        <div class="hg-service-icon">🥗</div>
                        <span>Nutrition Plan</span>
                    </a>

                    <a href="/HealthGuard/patient/my_notifications.php" class="hg-service-card">
                        <div class="hg-service-icon">🔔</div>
                        <span>Notifications</span>
                    </a>
                </div>
            </section>

            <section class="hg-panel">
                <div class="hg-tabs">
                    <span class="hg-tab active">About HealthGuard</span>
                </div>

                <div class="hg-panel-content">
                    <h3>About the Site</h3>
                    <p>
                        HealthGuard is a healthcare management system that helps patients
                        reach the right doctor according to illnesses and doctor specializations.
                        Instead of searching randomly, the patient can choose an illness and
                        the system will show suitable doctors in a simple and organized way.
                    </p>

                    <p>
                        The patient can also book appointments, remove appointments when needed,
                        contact doctors through messages, view personal information, check reports,
                        and follow a weekly nutrition plan created by the doctor from one modern dashboard.
                    </p>

                    <div class="hg-info-grid">
                        <div class="hg-info-box">
                            <h4>Doctor Matching</h4>
                            <p>Find doctors based on illness and specialization.</p>
                        </div>

                        <div class="hg-info-box">
                            <h4>Appointment Booking</h4>
                            <p>Choose your preferred date and time safely.</p>
                        </div>

                        <div class="hg-info-box">
                            <h4>Messaging</h4>
                            <p>Send messages to doctors and receive replies directly.</p>
                        </div>

                        <div class="hg-info-box">
                            <h4>Nutrition Plan</h4>
                            <p>View the weekly nutrition plan prepared by your doctor.</p>
                        </div>

                        <div class="hg-info-box">
                            <h4>Notifications</h4>
                            <p>Receive alerts for appointments, messages, reports, and updates.</p>
                        </div>

                        <div class="hg-info-box">
                            <h4>Personal Access</h4>
                            <p>View your profile, doctors, appointments, and reports in one place.</p>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <aside class="hg-side-menu">
            <div class="hg-side-title">Health Tips</div>

            <div class="hg-info-box">
                <h4>Stay Healthy</h4>
                <p>Drink enough water and maintain a balanced diet.</p>
            </div>

            <div class="hg-info-box">
                <h4>Appointments</h4>
                <p>Always arrive 10 minutes before your appointment time.</p>
            </div>

            <div class="hg-info-box">
                <h4>Nutrition</h4>
                <p>Follow the weekly nutrition plan created by your doctor carefully.</p>
            </div>
        </aside>

    </div>
</div>

</body>
</html>