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

if (isset($_POST['meal_name'])) {
    $meal_name = trim($_POST['meal_name']);
    $meal_time = trim($_POST['meal_time']);
    $date = trim($_POST['date']);
    $calories = trim($_POST['calories']);

    if ($meal_name == "" || $meal_time == "" || $date == "" || $calories == "") {
        $message = "Please fill all fields";
    } else {
        $sqlInsertMeal = "INSERT INTO meals (user_id, meal_name, meal_time, date, calories)
                          VALUES ('$user_id', '$meal_name', '$meal_time', '$date', '$calories')";

        if (mysqli_query($conn, $sqlInsertMeal)) {

            $sqlSum = "SELECT IFNULL(SUM(calories),0) AS total_calories
                       FROM meals
                       WHERE user_id='$user_id' AND date='$date'";
            $resultSum = mysqli_query($conn, $sqlSum);
            $rowSum = mysqli_fetch_assoc($resultSum);
            $total_calories = $rowSum['total_calories'];

            $sqlCheckTrack = "SELECT * FROM nutrition_tracking WHERE user_id='$user_id' AND date='$date'";
            $resultCheckTrack = mysqli_query($conn, $sqlCheckTrack);

            if (mysqli_num_rows($resultCheckTrack) > 0) {
                mysqli_query($conn, "UPDATE nutrition_tracking
                                     SET total_calories='$total_calories'
                                     WHERE user_id='$user_id' AND date='$date'");
            } else {
                mysqli_query($conn, "INSERT INTO nutrition_tracking (user_id, total_calories, date)
                                     VALUES ('$user_id', '$total_calories', '$date')");
            }

            $message = "Meal added successfully";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

$sqlToday = "SELECT IFNULL(SUM(calories),0) AS total_today
             FROM meals
             WHERE user_id='$user_id' AND date=CURDATE()";
$resultToday = mysqli_query($conn, $sqlToday);
$rowToday = mysqli_fetch_assoc($resultToday);
$total_today = $rowToday['total_today'];

$sqlMeals = "SELECT * FROM meals
             WHERE user_id='$user_id'
             ORDER BY date DESC, meal_time DESC";
$resultMeals = mysqli_query($conn, $sqlMeals);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Nutrition</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Nutrition</h1>
    <p class="section-subtitle">Track your meals and calories</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/patient/patient_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Add Meal</h3>

            <?php if (!empty($message)) { ?>
                <div class="message"><?php echo $message; ?></div>
            <?php } ?>

            <form method="POST">
                <label>Meal Name</label>
                <input type="text" name="meal_name" placeholder="Example: Chicken rice">

                <label>Meal Time</label>
                <input type="time" name="meal_time">

                <label>Date</label>
                <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>">

                <label>Calories</label>
                <input type="number" name="calories" placeholder="Example: 500">

                <button type="submit">Add Meal</button>
            </form>
        </div>

        <div class="card">
            <h3>Today Summary</h3>
            <p><strong>Total Calories Today:</strong> <?php echo $total_today; ?></p>

            <?php if ($total_today == 0) { ?>
                <p>No meals added today yet.</p>
            <?php } elseif ($total_today < 1200) { ?>
                <p>Your calories today are low.</p>
            <?php } elseif ($total_today <= 2500) { ?>
                <p>Your calorie intake today looks balanced.</p>
            <?php } else { ?>
                <p>Your calorie intake today is high.</p>
            <?php } ?>
        </div>
    </div>

    <div class="table-wrapper" style="margin-top:25px;">
        <table>
            <tr>
                <th>Meal Name</th>
                <th>Meal Time</th>
                <th>Date</th>
                <th>Calories</th>
                <th>Action</th>
            </tr>

            <?php if (mysqli_num_rows($resultMeals) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($resultMeals)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['meal_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['meal_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['calories']); ?></td>
                    <td>
                        <a href="/HealthGuard/patient/delete_meal.php?meal_id=<?php echo $row['meal_id']; ?>" class="danger-btn" onclick="return confirm('Delete this meal?');">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="5">No meals yet.</td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

</body>
</html>
<?php CloseCon($conn); ?>