<?php
// start session
session_start();

// check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    // redirect to login
    header("Location: /HealthGuard/login.php");
    // stop script
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Illness Matching</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">Illness Matching</h1>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/admin/admin_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>Illness Type</th>
                <th>Specialization</th>
            </tr>
            <tr>
                <td>Heart Problem</td>
                <td>Cardiologist</td>
            </tr>
            <tr>
                <td>Skin Problem</td>
                <td>Dermatologist</td>
            </tr>
            <tr>
                <td>Eye Problem</td>
                <td>Ophthalmologist</td>
            </tr>
            <tr>
                <td>Bone Problem</td>
                <td>Orthopedic</td>
            </tr>
            <tr>
                <td>Teeth Problem</td>
                <td>Dentist</td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>