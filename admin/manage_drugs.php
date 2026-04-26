<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: /HealthGuard/login.php");
    exit();
}

$conn = OpenCon();
$sql = "SELECT * FROM drugs ORDER BY drug_name ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Drugs</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="admin-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">Manage Drugs</h1>
    <p class="section-subtitle">Medicines used in doctor prescriptions</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/admin/add_drug.php" class="primary-btn">Add Drug</a>
        <a href="/HealthGuard/admin/admin_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>ID</th>
                <th>Drug Name</th>
                <th>Allergy Group</th>
                <th>Notes</th>
                <th>Action</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['drug_id']; ?></td>
                <td><?php echo htmlspecialchars($row['drug_name']); ?></td>
                <td><?php echo htmlspecialchars($row['allergy_group']); ?></td>
                <td><?php echo htmlspecialchars($row['notes']); ?></td>
                <td>
                    <a href="/HealthGuard/admin/delete_drug.php?drug_id=<?php echo $row['drug_id']; ?>" class="danger-btn" onclick="return confirm('Delete this drug?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>
<?php CloseCon($conn); ?>