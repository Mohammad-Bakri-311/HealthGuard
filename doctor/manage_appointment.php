<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "doctor") {
    header("Location: /HealthGuard/login.php");
    exit();
}

if (!isset($_GET['appointment_id'])) {
    header("Location: /HealthGuard/doctor/my_appointments.php");
    exit();
}

$conn = OpenCon();

$appointment_id = $_GET['appointment_id'];
$doctor_user_id = $_SESSION['user_id'];
$message = "";

$sqlDoctor = "SELECT * FROM doctors WHERE user_id='$doctor_user_id'";
$resultDoctor = mysqli_query($conn, $sqlDoctor);
$doctor = mysqli_fetch_assoc($resultDoctor);

if (!$doctor) {
    CloseCon($conn);
    header("Location: /HealthGuard/doctor/doctor_dashboard.php");
    exit();
}

$doctor_id = $doctor['doctor_id'];

$sqlAppointment = "SELECT appointments.*, users.name AS patient_name, users.email AS patient_email
                   FROM appointments
                   JOIN users ON appointments.patient_id = users.user_id
                   WHERE appointments.appointment_id='$appointment_id'
                   AND appointments.doctor_id='$doctor_id'";
$resultAppointment = mysqli_query($conn, $sqlAppointment);
$appointment = mysqli_fetch_assoc($resultAppointment);

if (!$appointment) {
    CloseCon($conn);
    header("Location: /HealthGuard/doctor/my_appointments.php");
    exit();
}

$patient_id = $appointment['patient_id'];

$sqlExistingReport = "SELECT * FROM medical_reports WHERE appointment_id='$appointment_id'";
$resultExistingReport = mysqli_query($conn, $sqlExistingReport);
$existingReport = mysqli_fetch_assoc($resultExistingReport);

if (isset($_POST['diagnosis'])) {
    $diagnosis = trim($_POST['diagnosis']);
    $examination = trim($_POST['examination']);
    $treatment_done = trim($_POST['treatment_done']);
    $doctor_notes = trim($_POST['doctor_notes']);
    $follow_up_needed = trim($_POST['follow_up_needed']);
    $follow_up_note = trim($_POST['follow_up_note']);
    $follow_up_date = trim($_POST['follow_up_date']);
    $follow_up_time = trim($_POST['follow_up_time']);

    $drug_ids = isset($_POST['drug_id']) ? $_POST['drug_id'] : array();
    $dosages = isset($_POST['dosage']) ? $_POST['dosage'] : array();
    $frequencies = isset($_POST['frequency']) ? $_POST['frequency'] : array();
    $durations = isset($_POST['duration']) ? $_POST['duration'] : array();
    $instructions = isset($_POST['instructions']) ? $_POST['instructions'] : array();

    if ($diagnosis == "") {
        $message = "Diagnosis is required";
    } else {
        $warning = "";

        $sqlAllergies = "SELECT * FROM patient_allergies WHERE patient_id='$patient_id'";
        $resultAllergies = mysqli_query($conn, $sqlAllergies);

        $allergy_names = array();
        while ($rowAllergy = mysqli_fetch_assoc($resultAllergies)) {
            $allergy_names[] = strtolower(trim($rowAllergy['allergy_name']));
        }

        for ($i = 0; $i < count($drug_ids); $i++) {
            $selected_drug_id = trim($drug_ids[$i]);

            if ($selected_drug_id != "") {
                $sqlDrug = "SELECT * FROM drugs WHERE drug_id='$selected_drug_id'";
                $resultDrug = mysqli_query($conn, $sqlDrug);
                $drug = mysqli_fetch_assoc($resultDrug);

                if ($drug) {
                    $drug_name = strtolower(trim($drug['drug_name']));
                    $allergy_group = strtolower(trim($drug['allergy_group']));

                    if (in_array($drug_name, $allergy_names) || ($allergy_group != "" && in_array($allergy_group, $allergy_names))) {
                        $warning = "Warning: patient has allergy to selected drug or allergy group.";
                        break;
                    }
                }
            }
        }

        if ($warning != "") {
            $message = $warning;
        } else {
            if ($existingReport) {
                $report_id = $existingReport['report_id'];

                $sqlUpdate = "UPDATE medical_reports
                              SET patient_id='$patient_id',
                                  doctor_id='$doctor_id',
                                  diagnosis='$diagnosis',
                                  examination='$examination',
                                  treatment_done='$treatment_done',
                                  doctor_notes='$doctor_notes',
                                  follow_up_needed='$follow_up_needed',
                                  follow_up_note='$follow_up_note',
                                  updated_at=NOW()
                              WHERE report_id='$report_id'";
                mysqli_query($conn, $sqlUpdate);

                mysqli_query($conn, "DELETE FROM report_drugs WHERE report_id='$report_id'");
            } else {
                $sqlInsert = "INSERT INTO medical_reports
                              (appointment_id, patient_id, doctor_id, diagnosis, examination, treatment_done, doctor_notes, follow_up_needed, follow_up_note, created_at, updated_at)
                              VALUES
                              ('$appointment_id', '$patient_id', '$doctor_id', '$diagnosis', '$examination', '$treatment_done', '$doctor_notes', '$follow_up_needed', '$follow_up_note', NOW(), NOW())";
                mysqli_query($conn, $sqlInsert);
                $report_id = mysqli_insert_id($conn);
            }

            for ($i = 0; $i < count($drug_ids); $i++) {
                $selected_drug_id = trim($drug_ids[$i]);
                $dose = trim($dosages[$i]);
                $freq = trim($frequencies[$i]);
                $dur = trim($durations[$i]);
                $instr = trim($instructions[$i]);

                if ($selected_drug_id != "" && $dose != "" && $freq != "" && $dur != "") {
                    $sqlDrugInsert = "INSERT INTO report_drugs
                                      (report_id, drug_id, dosage, frequency, duration, instructions, created_at)
                                      VALUES
                                      ('$report_id', '$selected_drug_id', '$dose', '$freq', '$dur', '$instr', NOW())";
                    mysqli_query($conn, $sqlDrugInsert);
                }
            }

            mysqli_query($conn, "UPDATE appointments
                                 SET status='completed'
                                 WHERE appointment_id='$appointment_id'");

            // add report notification
            mysqli_query($conn, "INSERT INTO notifications (user_id, type, created_at, message, status)
                                 VALUES ('$patient_id', 'report', NOW(), 'A new medical report has been added to your account.', 'unread')");

            if ($follow_up_needed == "yes" && $follow_up_date != "" && $follow_up_time != "") {
                $checkFollow = "SELECT * FROM appointments
                                WHERE doctor_id='$doctor_id'
                                AND appointment_date='$follow_up_date'
                                AND appointment_time='$follow_up_time'";
                $resultFollow = mysqli_query($conn, $checkFollow);

                if (mysqli_num_rows($resultFollow) == 0) {
                    mysqli_query($conn, "INSERT INTO appointments
                                         (patient_id, doctor_id, appointment_date, appointment_time, status, created_at)
                                         VALUES
                                         ('$patient_id', '$doctor_id', '$follow_up_date', '$follow_up_time', 'pending', NOW())");

                    // add follow-up notification
                    mysqli_query($conn, "INSERT INTO notifications (user_id, type, created_at, message, status)
                                         VALUES ('$patient_id', 'follow_up', NOW(), 'A follow-up appointment has been created for you.', 'unread')");

                    $message = "Report saved successfully and follow-up appointment created.";
                } else {
                    $message = "Report saved successfully, but follow-up time is already booked.";
                }
            } else {
                $message = "Report saved successfully.";
            }

            $resultExistingReport = mysqli_query($conn, "SELECT * FROM medical_reports WHERE appointment_id='$appointment_id'");
            $existingReport = mysqli_fetch_assoc($resultExistingReport);
        }
    }
}

$sqlAllergiesView = "SELECT * FROM patient_allergies WHERE patient_id='$patient_id' ORDER BY allergy_name ASC";
$resultAllergiesView = mysqli_query($conn, $sqlAllergiesView);

$sqlDrugs = "SELECT * FROM drugs ORDER BY drug_name ASC";
$resultDrugs = mysqli_query($conn, $sqlDrugs);

$existingDrugs = array();
if ($existingReport) {
    $report_id_now = $existingReport['report_id'];
    $sqlExistingDrugs = "SELECT report_drugs.*, drugs.drug_name
                         FROM report_drugs
                         JOIN drugs ON report_drugs.drug_id = drugs.drug_id
                         WHERE report_drugs.report_id='$report_id_now'";
    $resultExistingDrugs = mysqli_query($conn, $sqlExistingDrugs);

    while ($rowDrug = mysqli_fetch_assoc($resultExistingDrugs)) {
        $existingDrugs[] = $rowDrug;
    }
}

$drugsList = array();
$resultDrugs2 = mysqli_query($conn, "SELECT * FROM drugs ORDER BY drug_name ASC");
while ($rowDrug2 = mysqli_fetch_assoc($resultDrugs2)) {
    $drugsList[] = $rowDrug2;
}

$rowsToShow = 3;
if (count($existingDrugs) > 3) {
    $rowsToShow = count($existingDrugs);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointment</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="doctor-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">Visit Report</h1>
    <p class="section-subtitle">Doctor report after finishing the appointment</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/doctor/my_appointments.php" class="secondary-btn">Back to Appointments</a>
        <a href="/HealthGuard/doctor/my_reports.php" class="secondary-btn">My Reports</a>
    </div>

    <?php if (!empty($message)) { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <div class="cards">
        <div class="card">
            <h3>Appointment Information</h3>
            <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($appointment['patient_email']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($appointment['status']); ?></p>
        </div>

        <div class="card">
            <h3>Patient Allergies</h3>
            <?php if (mysqli_num_rows($resultAllergiesView) > 0) { ?>
                <?php while ($rowAllergyView = mysqli_fetch_assoc($resultAllergiesView)) { ?>
                    <p>
                        <strong><?php echo htmlspecialchars($rowAllergyView['allergy_name']); ?></strong>
                        <?php if ($rowAllergyView['reaction'] != "") { ?>
                            - <?php echo htmlspecialchars($rowAllergyView['reaction']); ?>
                        <?php } ?>
                    </p>
                <?php } ?>
            <?php } else { ?>
                <p>No allergies saved by patient.</p>
            <?php } ?>
        </div>
    </div>

    <div class="form-container" style="min-height:auto; padding-top:20px;">
        <form class="form-box" method="POST" style="max-width:1100px;">
            <h2>Medical Report</h2>
            <p class="subtitle">Write what happened during the appointment</p>

            <label>Diagnosis</label>
            <textarea name="diagnosis"><?php echo $existingReport ? htmlspecialchars($existingReport['diagnosis']) : ""; ?></textarea>

            <label>Examination</label>
            <textarea name="examination"><?php echo $existingReport ? htmlspecialchars($existingReport['examination']) : ""; ?></textarea>

            <label>Treatment Done</label>
            <textarea name="treatment_done"><?php echo $existingReport ? htmlspecialchars($existingReport['treatment_done']) : ""; ?></textarea>

            <label>Doctor Notes</label>
            <textarea name="doctor_notes"><?php echo $existingReport ? htmlspecialchars($existingReport['doctor_notes']) : ""; ?></textarea>

            <h3 style="margin-top:25px; margin-bottom:15px;">Prescribed Drugs</h3>

            <div class="table-wrapper">
                <table>
                    <tr>
                        <th>Drug</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Instructions</th>
                    </tr>

                    <?php for ($i = 0; $i < $rowsToShow; $i++) { ?>
                    <tr>
                        <td>
                            <select name="drug_id[]">
                                <option value="">Choose drug</option>
                                <?php foreach ($drugsList as $oneDrug) { ?>
                                    <?php
                                    $selected = "";
                                    if (isset($existingDrugs[$i]) && $existingDrugs[$i]['drug_id'] == $oneDrug['drug_id']) {
                                        $selected = "selected";
                                    }
                                    ?>
                                    <option value="<?php echo $oneDrug['drug_id']; ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($oneDrug['drug_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="dosage[]" value="<?php echo isset($existingDrugs[$i]) ? htmlspecialchars($existingDrugs[$i]['dosage']) : ""; ?>" placeholder="Example: 500mg">
                        </td>
                        <td>
                            <input type="text" name="frequency[]" value="<?php echo isset($existingDrugs[$i]) ? htmlspecialchars($existingDrugs[$i]['frequency']) : ""; ?>" placeholder="Example: 2 times daily">
                        </td>
                        <td>
                            <input type="text" name="duration[]" value="<?php echo isset($existingDrugs[$i]) ? htmlspecialchars($existingDrugs[$i]['duration']) : ""; ?>" placeholder="Example: 7 days">
                        </td>
                        <td>
                            <input type="text" name="instructions[]" value="<?php echo isset($existingDrugs[$i]) ? htmlspecialchars($existingDrugs[$i]['instructions']) : ""; ?>" placeholder="After food / before sleep">
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>

            <h3 style="margin-top:25px; margin-bottom:15px;">Follow-up Appointment</h3>

            <label>Need Follow-up?</label>
            <select name="follow_up_needed">
                <option value="no" <?php echo ($existingReport && $existingReport['follow_up_needed'] == 'no') ? 'selected' : ''; ?>>No</option>
                <option value="yes" <?php echo ($existingReport && $existingReport['follow_up_needed'] == 'yes') ? 'selected' : ''; ?>>Yes</option>
            </select>

            <label>Follow-up Note</label>
            <textarea name="follow_up_note"><?php echo $existingReport ? htmlspecialchars($existingReport['follow_up_note']) : ""; ?></textarea>

            <label>Follow-up Date</label>
            <input type="date" name="follow_up_date">

            <label>Follow-up Time</label>
            <input type="time" name="follow_up_time">

            <button type="submit">Save Report</button>
        </form>
    </div>
</div>

</body>
</html>
<?php CloseCon($conn); ?>