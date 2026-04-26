<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$conn = OpenCon();
$patient_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['allergy_name'])) {
    $allergy_name = trim($_POST['allergy_name']);
    $reaction = trim($_POST['reaction']);

    if ($allergy_name == "") {
        $message = "Please enter allergy name";
    } else {
        $check = "SELECT * FROM patient_allergies
                  WHERE patient_id='$patient_id' AND allergy_name='$allergy_name'";
        $resultCheck = mysqli_query($conn, $check);

        if (mysqli_num_rows($resultCheck) > 0) {
            $message = "Allergy already added";
        } else {
            $sqlInsert = "INSERT INTO patient_allergies (patient_id, allergy_name, reaction, created_at)
                          VALUES ('$patient_id', '$allergy_name', '$reaction', NOW())";

            if (mysqli_query($conn, $sqlInsert)) {
                $message = "Allergy added successfully";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM patient_allergies WHERE allergy_id='$delete_id' AND patient_id='$patient_id'");
    header("Location: /HealthGuard/patient/my_allergies.php");
    exit();
}

$sql = "SELECT * FROM patient_allergies WHERE patient_id='$patient_id' ORDER BY allergy_name ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Allergies</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Allergies</h1>
    <p class="section-subtitle">Add your allergies so the doctor can prescribe medicines safely</p>

    <div class="cards">
        <div class="card">
            <h3>Add Allergy</h3>

            <?php if (!empty($message)) { ?>
                <div class="message"><?php echo $message; ?></div>
            <?php } ?>

            <form method="POST">
                <label>Allergy Name</label>
                <input type="text" name="allergy_name" placeholder="Example: Penicillin / NSAID / Ibuprofen">

                <label>Reaction</label>
                <input type="text" name="reaction" placeholder="Example: rash / swelling / breathing problem">

                <button type="submit">Add Allergy</button>
            </form>
        </div>

        <div class="card">
            <h3>Saved Allergies</h3>

            <div class="table-wrapper">
                <table>
                    <tr>
                        <th>Allergy</th>
                        <th>Reaction</th>
                        <th>Action</th>
                    </tr>

                    <?php if (mysqli_num_rows($result) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['allergy_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['reaction']); ?></td>
                            <td>
                                <a href="/HealthGuard/patient/my_allergies.php?delete_id=<?php echo $row['allergy_id']; ?>" class="danger-btn" onclick="return confirm('Delete this allergy?');">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3">No allergies added yet.</td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php CloseCon($conn); ?>