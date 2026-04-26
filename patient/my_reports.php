<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$conn = OpenCon();
$patient_id = $_SESSION['user_id'];

$sql = "SELECT medical_reports.*, appointments.appointment_date, appointments.appointment_time,
               users.name AS doctor_name, users.email AS doctor_email, doctors.specialization
        FROM medical_reports
        JOIN appointments ON medical_reports.appointment_id = appointments.appointment_id
        JOIN doctors ON medical_reports.doctor_id = doctors.doctor_id
        JOIN users ON doctors.user_id = users.user_id
        WHERE medical_reports.patient_id='$patient_id'
        ORDER BY medical_reports.created_at DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Reports</h1>
    <p class="section-subtitle">Doctor reports after finishing your appointments</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/patient/patient_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <div class="cards">
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="card">
                    <h3>Dr. <?php echo htmlspecialchars($row['doctor_name']); ?></h3>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['doctor_email']); ?></p>
                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($row['specialization']); ?></p>
                    <p><strong>Appointment:</strong> <?php echo htmlspecialchars($row['appointment_date']); ?> - <?php echo htmlspecialchars($row['appointment_time']); ?></p>
                    <p><strong>Diagnosis:</strong> <?php echo nl2br(htmlspecialchars($row['diagnosis'])); ?></p>
                    <p><strong>Examination:</strong> <?php echo nl2br(htmlspecialchars($row['examination'])); ?></p>
                    <p><strong>Treatment Done:</strong> <?php echo nl2br(htmlspecialchars($row['treatment_done'])); ?></p>
                    <p><strong>Doctor Notes:</strong> <?php echo nl2br(htmlspecialchars($row['doctor_notes'])); ?></p>
                    <p><strong>Follow-up Needed:</strong> <?php echo htmlspecialchars($row['follow_up_needed']); ?></p>
                    <p><strong>Follow-up Note:</strong> <?php echo nl2br(htmlspecialchars($row['follow_up_note'])); ?></p>

                    <div class="table-wrapper" style="margin-top:18px;">
                        <table>
                            <tr>
                                <th>Drug</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                                <th>Instructions</th>
                            </tr>

                            <?php
                            $report_id = $row['report_id'];
                            $sqlDrugs = "SELECT report_drugs.*, drugs.drug_name
                                         FROM report_drugs
                                         JOIN drugs ON report_drugs.drug_id = drugs.drug_id
                                         WHERE report_drugs.report_id='$report_id'";
                            $resultDrugs = mysqli_query($conn, $sqlDrugs);
                            ?>

                            <?php if (mysqli_num_rows($resultDrugs) > 0) { ?>
                                <?php while ($rowDrug = mysqli_fetch_assoc($resultDrugs)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rowDrug['drug_name']); ?></td>
                                    <td><?php echo htmlspecialchars($rowDrug['dosage']); ?></td>
                                    <td><?php echo htmlspecialchars($rowDrug['frequency']); ?></td>
                                    <td><?php echo htmlspecialchars($rowDrug['duration']); ?></td>
                                    <td><?php echo htmlspecialchars($rowDrug['instructions']); ?></td>
                                </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5">No medicines prescribed.</td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="card">
                <h3>No Reports Yet</h3>
                <p>No doctor has created a report for your appointments yet.</p>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>
<?php CloseCon($conn); ?>