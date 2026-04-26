<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$conn = OpenCon();
$user_id = $_SESSION['user_id'];
$message = "";

// save patient extra info
if (isset($_POST['age'])) {
    $age = trim($_POST['age'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $medical_notes = trim($_POST['medical_notes'] ?? '');

    $sqlCheck = "SELECT * FROM patients WHERE user_id='$user_id'";
    $resultCheck = mysqli_query($conn, $sqlCheck);

    if (mysqli_num_rows($resultCheck) > 0) {
        $sqlUpdate = "UPDATE patients
                      SET age=" . ($age === "" ? "NULL" : "'$age'") . ",
                          gender=" . ($gender === "" ? "NULL" : "'$gender'") . ",
                          medical_notes=" . ($medical_notes === "" ? "NULL" : "'$medical_notes'") . "
                      WHERE user_id='$user_id'";
        mysqli_query($conn, $sqlUpdate);
    } else {
        $sqlInsert = "INSERT INTO patients (user_id, age, gender, medical_notes)
                      VALUES (
                          '$user_id',
                          " . ($age === "" ? "NULL" : "'$age'") . ",
                          " . ($gender === "" ? "NULL" : "'$gender'") . ",
                          " . ($medical_notes === "" ? "NULL" : "'$medical_notes'") . "
                      )";
        mysqli_query($conn, $sqlInsert);
    }

    $message = "Profile updated successfully";
}

$sql = "SELECT users.name, users.email, users.created_at, patients.age, patients.gender, patients.medical_notes
        FROM users
        LEFT JOIN patients ON users.user_id = patients.user_id
        WHERE users.user_id='$user_id' AND users.role='patient'";

$result = mysqli_query($conn, $sql);
$patient = mysqli_fetch_assoc($result);

// safe values
$patient_name = isset($patient['name']) && $patient['name'] !== null ? $patient['name'] : '';
$patient_email = isset($patient['email']) && $patient['email'] !== null ? $patient['email'] : '';
$patient_created_at = isset($patient['created_at']) && $patient['created_at'] !== null ? $patient['created_at'] : '';
$patient_age = isset($patient['age']) && $patient['age'] !== null ? (string)$patient['age'] : '';
$patient_gender = isset($patient['gender']) && $patient['gender'] !== null ? $patient['gender'] : '';
$patient_medical_notes = isset($patient['medical_notes']) && $patient['medical_notes'] !== null ? $patient['medical_notes'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="form-container">
    <form class="form-box" method="POST">
        <h2>My Profile</h2>
        <p class="subtitle">Patient account information</p>

        <div class="hero-actions" style="justify-content:center; margin-bottom:20px;">
            <a href="/HealthGuard/patient/patient_dashboard.php" class="secondary-btn">Back to Dashboard</a>
        </div>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php } ?>

        <label>Patient Name</label>
        <input type="text" value="<?php echo htmlspecialchars($patient_name); ?>" readonly>

        <label>Email</label>
        <input type="text" value="<?php echo htmlspecialchars($patient_email); ?>" readonly>

        <label>Created At</label>
        <input type="text" value="<?php echo htmlspecialchars($patient_created_at); ?>" readonly>

        <label>Age</label>
        <input type="number" name="age" value="<?php echo htmlspecialchars($patient_age); ?>">

        <label>Gender</label>
        <select name="gender">
            <option value="">Choose gender</option>
            <option value="male" <?php if ($patient_gender == 'male') echo 'selected'; ?>>Male</option>
            <option value="female" <?php if ($patient_gender == 'female') echo 'selected'; ?>>Female</option>
            <option value="other" <?php if ($patient_gender == 'other') echo 'selected'; ?>>Other</option>
        </select>

        <label>Medical Notes</label>
        <textarea name="medical_notes"><?php echo htmlspecialchars($patient_medical_notes); ?></textarea>

        <button type="submit">Save Profile</button>
    </form>
</div>

</body>
</html>
<?php CloseCon($conn); ?>