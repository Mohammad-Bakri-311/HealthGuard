<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$conn = OpenCon();
$patient_id = $_SESSION['user_id'];

// get latest week from nutrition plans
$sqlLatestWeek = "SELECT week_start
                  FROM nutrition_plans
                  WHERE patient_id='$patient_id'
                  ORDER BY week_start DESC
                  LIMIT 1";
$resultLatestWeek = mysqli_query($conn, $sqlLatestWeek);
$latestWeek = mysqli_fetch_assoc($resultLatestWeek);

// choose week_start safely
if (isset($_GET['week_start']) && trim($_GET['week_start']) != "") {
    $week_start = trim($_GET['week_start']);
} elseif ($latestWeek && !empty($latestWeek['week_start'])) {
    $week_start = $latestWeek['week_start'];
} else {
    $week_start = "";
}

$result = null;

// run query only if week_start is not empty
if ($week_start != "") {
    $sql = "SELECT nutrition_plans.*, users.name AS doctor_name, users.email AS doctor_email
            FROM nutrition_plans
            JOIN doctors ON nutrition_plans.doctor_id = doctors.doctor_id
            JOIN users ON doctors.user_id = users.user_id
            WHERE nutrition_plans.patient_id='$patient_id'
            AND nutrition_plans.week_start='$week_start'
            ORDER BY FIELD(day_name,'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')";
    $result = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Nutrition Plan</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Weekly Nutrition Plan</h1>
    <p class="section-subtitle">Weekly meal plan prepared by your doctor</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/patient/patient_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <?php if ($week_start != "") { ?>
        <div class="card" style="margin-bottom:20px;">
            <p><strong>Week Start:</strong> <?php echo htmlspecialchars($week_start); ?></p>
        </div>

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>Day</th>
                    <th>Breakfast</th>
                    <th>Lunch</th>
                    <th>Dinner</th>
                    <th>Snack</th>
                    <th>Calories Goal</th>
                    <th>Illness Advice</th>
                    <th>Doctor</th>
                </tr>

                <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['day_name'] ?? ''); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['breakfast'] ?? '')); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['lunch'] ?? '')); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['dinner'] ?? '')); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['snack'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars((string)($row['calories_goal'] ?? '')); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['illness_advice'] ?? '')); ?></td>
                        <td>
                            Dr. <?php echo htmlspecialchars($row['doctor_name'] ?? ''); ?><br>
                            <?php echo htmlspecialchars($row['doctor_email'] ?? ''); ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="8">No nutrition plan found for this week.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } else { ?>
        <div class="card">
            <h3>No Nutrition Plan Yet</h3>
            <p>Your doctor has not added a weekly nutrition plan yet.</p>
        </div>
    <?php } ?>
</div>

</body>
</html>
<?php CloseCon($conn); ?>