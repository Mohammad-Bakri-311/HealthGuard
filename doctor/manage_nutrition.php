<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "doctor") {
    header("Location: /HealthGuard/login.php");
    exit();
}

if (!isset($_GET['patient_id'])) {
    header("Location: /HealthGuard/doctor/my_patients.php");
    exit();
}

$conn = OpenCon();

$doctor_user_id = $_SESSION['user_id'];
$patient_id = $_GET['patient_id'];
$message = "";

$sqlDoctor = "SELECT * FROM doctors WHERE user_id='$doctor_user_id'";
$resultDoctor = mysqli_query($conn, $sqlDoctor);
$doctor = mysqli_fetch_assoc($resultDoctor);

if (!$doctor) {
    CloseCon($conn);
    header("Location: /HealthGuard/login.php");
    exit();
}

$doctor_id = $doctor['doctor_id'];

$sqlPatient = "SELECT * FROM users WHERE user_id='$patient_id' AND role='patient'";
$resultPatient = mysqli_query($conn, $sqlPatient);
$patient = mysqli_fetch_assoc($resultPatient);

if (!$patient) {
    CloseCon($conn);
    header("Location: /HealthGuard/doctor/my_patients.php");
    exit();
}

// latest diagnosis
$sqlLatestReport = "SELECT diagnosis
                    FROM medical_reports
                    WHERE patient_id='$patient_id'
                    ORDER BY created_at DESC
                    LIMIT 1";
$resultLatestReport = mysqli_query($conn, $sqlLatestReport);
$latestReport = mysqli_fetch_assoc($resultLatestReport);
$latest_diagnosis = $latestReport ? $latestReport['diagnosis'] : "No diagnosis found yet";

// allergies
$sqlAllergies = "SELECT * FROM patient_allergies WHERE patient_id='$patient_id' ORDER BY allergy_name ASC";
$resultAllergies = mysqli_query($conn, $sqlAllergies);

$week_start = isset($_POST['week_start']) ? $_POST['week_start'] : date('Y-m-d');
$days = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

if (isset($_POST['save_plan'])) {
    mysqli_query($conn, "DELETE FROM nutrition_plans
                         WHERE patient_id='$patient_id'
                         AND week_start='$week_start'");

    foreach ($days as $day) {
        $breakfast = trim($_POST['breakfast'][$day] ?? '');
        $lunch = trim($_POST['lunch'][$day] ?? '');
        $dinner = trim($_POST['dinner'][$day] ?? '');
        $snack = trim($_POST['snack'][$day] ?? '');
        $calories_goal = trim($_POST['calories_goal'][$day] ?? '');
        $illness_advice = trim($_POST['illness_advice'][$day] ?? '');

        $calories_sql = $calories_goal === "" ? "NULL" : "'" . mysqli_real_escape_string($conn, $calories_goal) . "'";

        $sqlInsert = "INSERT INTO nutrition_plans
                      (patient_id, doctor_id, week_start, day_name, breakfast, lunch, dinner, snack, calories_goal, illness_advice, created_at, updated_at)
                      VALUES
                      (
                        '$patient_id',
                        '$doctor_id',
                        '$week_start',
                        '$day',
                        '" . mysqli_real_escape_string($conn, $breakfast) . "',
                        '" . mysqli_real_escape_string($conn, $lunch) . "',
                        '" . mysqli_real_escape_string($conn, $dinner) . "',
                        '" . mysqli_real_escape_string($conn, $snack) . "',
                        $calories_sql,
                        '" . mysqli_real_escape_string($conn, $illness_advice) . "',
                        NOW(),
                        NOW()
                      )";
        mysqli_query($conn, $sqlInsert);
    }

    mysqli_query($conn, "INSERT INTO notifications (user_id, type, created_at, message, status)
                         VALUES ('$patient_id', 'nutrition', NOW(), 'Your doctor has added or updated your weekly nutrition plan.', 'unread')");

    $message = "Weekly nutrition plan saved successfully.";
}

// get current plan for selected week
$sqlPlan = "SELECT * FROM nutrition_plans
            WHERE patient_id='$patient_id'
            AND week_start='$week_start'
            ORDER BY FIELD(day_name,'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday')";
$resultPlan = mysqli_query($conn, $sqlPlan);

$planData = array();
while ($rowPlan = mysqli_fetch_assoc($resultPlan)) {
    $planData[$rowPlan['day_name']] = $rowPlan;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Nutrition Plan</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="doctor-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">Weekly Nutrition Plan</h1>
    <p class="section-subtitle">Create a full weekly meal plan for the patient</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/doctor/my_patients.php" class="secondary-btn">Back to Patients</a>
    </div>

    <?php if (!empty($message)) { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <div class="cards">
        <div class="card">
            <h3>Patient Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($patient['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
            <p><strong>Latest Diagnosis:</strong> <?php echo htmlspecialchars($latest_diagnosis); ?></p>

            <h4 style="margin-top:15px;">Allergies</h4>
            <?php if (mysqli_num_rows($resultAllergies) > 0) { ?>
                <?php while ($rowAllergy = mysqli_fetch_assoc($resultAllergies)) { ?>
                    <p>
                        <strong><?php echo htmlspecialchars($rowAllergy['allergy_name']); ?></strong>
                        <?php if (!empty($rowAllergy['reaction'])) { ?>
                            - <?php echo htmlspecialchars($rowAllergy['reaction']); ?>
                        <?php } ?>
                    </p>
                <?php } ?>
            <?php } else { ?>
                <p>No allergies found.</p>
            <?php } ?>
        </div>
    </div>

    <div class="form-container" style="min-height:auto; padding-top:20px;">
        <form class="form-box" method="POST" style="max-width:1200px;">
            <h2>Plan Details</h2>
            <p class="subtitle">Create meals for all days of the week</p>

            <label>Week Start Date</label>
            <input type="date" name="week_start" value="<?php echo htmlspecialchars($week_start); ?>">

            <div class="table-wrapper" style="margin-top:20px;">
                <table>
                    <tr>
                        <th>Day</th>
                        <th>Breakfast</th>
                        <th>Lunch</th>
                        <th>Dinner</th>
                        <th>Snack</th>
                        <th>Calories Goal</th>
                        <th>Illness Advice</th>
                    </tr>

                    <?php foreach ($days as $day) { ?>
                    <tr>
                        <td><strong><?php echo $day; ?></strong></td>
                        <td>
                            <textarea name="breakfast[<?php echo $day; ?>]" placeholder="Breakfast"><?php echo isset($planData[$day]) ? htmlspecialchars($planData[$day]['breakfast']) : ""; ?></textarea>
                        </td>
                        <td>
                            <textarea name="lunch[<?php echo $day; ?>]" placeholder="Lunch"><?php echo isset($planData[$day]) ? htmlspecialchars($planData[$day]['lunch']) : ""; ?></textarea>
                        </td>
                        <td>
                            <textarea name="dinner[<?php echo $day; ?>]" placeholder="Dinner"><?php echo isset($planData[$day]) ? htmlspecialchars($planData[$day]['dinner']) : ""; ?></textarea>
                        </td>
                        <td>
                            <textarea name="snack[<?php echo $day; ?>]" placeholder="Snack"><?php echo isset($planData[$day]) ? htmlspecialchars($planData[$day]['snack']) : ""; ?></textarea>
                        </td>
                        <td>
                            <input type="number" name="calories_goal[<?php echo $day; ?>]" value="<?php echo isset($planData[$day]) ? htmlspecialchars($planData[$day]['calories_goal']) : ""; ?>" placeholder="Calories">
                        </td>
                        <td>
                            <textarea name="illness_advice[<?php echo $day; ?>]" placeholder="Advice for illness"><?php echo isset($planData[$day]) ? htmlspecialchars($planData[$day]['illness_advice']) : ""; ?></textarea>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>

            <button type="submit" name="save_plan">Save Weekly Plan</button>
        </form>
    </div>
</div>

</body>
</html>
<?php CloseCon($conn); ?>