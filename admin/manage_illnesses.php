<?php
// start session
session_start();

// include DB connection
include("../db_connection.php");

// check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    // redirect to login
    header("Location: /HealthGuard/login.php");
    // stop script
    exit();
}

// open DB connection
$conn = OpenCon();

// select all illnesses ordered by newest
$sql = "SELECT * FROM illnesses ORDER BY illness_id DESC";

// run query
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Illnesses</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">Manage Illnesses</h1>
    <p class="section-subtitle">Add and delete illness categories</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/admin/add_illness.php" class="primary-btn">Add Illness</a>
        <a href="/HealthGuard/admin/admin_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>ID</th>
                <th>Illness Name</th>
                <th>Specialization</th>
                <th>Action</th>
            </tr>

            <?php 
            // loop through results
            while ($row = mysqli_fetch_assoc($result)) { 
            ?>
            <tr>
                <td><?php echo $row['illness_id']; ?></td>
                <td><?php echo $row['illness_name']; ?></td>
                <td><?php echo $row['specialization']; ?></td>
                <td>
                    <a href="/HealthGuard/admin/delete_illness.php?illness_id=<?php echo $row['illness_id']; ?>" class="danger-btn">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>

<?php
// close DB connection
CloseCon($conn);
?>