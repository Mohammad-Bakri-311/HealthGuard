<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$message = "";

if (isset($_POST['drug_name'])) {
    $conn = OpenCon();

    $drug_name = trim($_POST['drug_name']);
    $allergy_group = trim($_POST['allergy_group']);
    $notes = trim($_POST['notes']);

    if ($drug_name == "") {
        $message = "Please enter drug name";
    } else {
        $check = "SELECT * FROM drugs WHERE drug_name='$drug_name'";
        $resultCheck = mysqli_query($conn, $check);

        if (mysqli_num_rows($resultCheck) > 0) {
            $message = "Drug already exists";
        } else {
            $sql = "INSERT INTO drugs (drug_name, allergy_group, notes, created_at)
                    VALUES ('$drug_name', '$allergy_group', '$notes', NOW())";

            if (mysqli_query($conn, $sql)) {
                $message = "Drug added successfully";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }

    CloseCon($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Drug</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="admin-page">

<?php include("../navbar.php"); ?>

<div class="form-container">
    <form class="form-box" method="POST">
        <h2>Add Drug</h2>
        <p class="subtitle">Create a medicine for prescriptions</p>

        <div class="hero-actions" style="justify-content:center; margin-bottom:20px;">
            <a href="/HealthGuard/admin/manage_drugs.php" class="secondary-btn">Back</a>
        </div>

        <?php if (!empty($message)) { ?>
            <div class="message"><?php echo $message; ?></div>
        <?php } ?>

        <label>Drug Name</label>
        <input type="text" name="drug_name" placeholder="Enter drug name">

        <label>Allergy Group</label>
        <input type="text" name="allergy_group" placeholder="Example: Penicillin / NSAID">

        <label>Notes</label>
        <textarea name="notes" placeholder="Optional notes"></textarea>

        <button type="submit">Add Drug</button>
    </form>
</div>

</body>
</html>