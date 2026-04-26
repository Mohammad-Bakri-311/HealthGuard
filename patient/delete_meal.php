<?php
session_start();
include("../db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    header("Location: /HealthGuard/login.php");
    exit();
}

if (!isset($_GET['meal_id'])) {
    header("Location: /HealthGuard/patient/my_nutrition.php");
    exit();
}

$conn = OpenCon();
$user_id = $_SESSION['user_id'];
$meal_id = $_GET['meal_id'];

$sqlMeal = "SELECT * FROM meals WHERE meal_id='$meal_id' AND user_id='$user_id'";
$resultMeal = mysqli_query($conn, $sqlMeal);
$meal = mysqli_fetch_assoc($resultMeal);

if ($meal) {
    $date = $meal['date'];

    mysqli_query($conn, "DELETE FROM meals WHERE meal_id='$meal_id' AND user_id='$user_id'");

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
    }
}

CloseCon($conn);
header("Location: /HealthGuard/patient/my_nutrition.php");
exit();
?>