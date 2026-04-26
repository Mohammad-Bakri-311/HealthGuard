<?php
// start session
session_start();

// include DB connection
include("../db_connection.php");

// check if patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "patient") {
    // redirect to login
    header("Location: /HealthGuard/login.php");
    // stop script
    exit();
}

// open DB connection
$conn = OpenCon();

// get patient id
$patient_id = $_SESSION['user_id'];

// message variable
$message = "";

// selected doctor id
$doctor_id = "";

// check if doctor selected
if (isset($_GET['doctor_id'])) {
    // get doctor id
    $doctor_id = $_GET['doctor_id'];
}

// send message
if (isset($_POST['doctor_id']) && isset($_POST['content'])) {
    // get doctor id
    $doctor_id = $_POST['doctor_id'];

    // get message text
    $content = trim($_POST['content']);

    // check empty message
    if ($content == "") {
        $message = "Please write a message";
    } else {
        // get doctor user_id from doctors table
        $sqlGetDoctorUser = "SELECT user_id FROM doctors WHERE doctor_id='$doctor_id'";
        $resultGetDoctorUser = mysqli_query($conn, $sqlGetDoctorUser);
        $doctorData = mysqli_fetch_assoc($resultGetDoctorUser);

        // if doctor exists
        if ($doctorData) {
            // store doctor user_id
            $doctor_user_id = $doctorData['user_id'];

            // insert message
            $sqlInsert = "INSERT INTO messages (sender_id, receiver_id, content, timestamp)
                          VALUES ('$patient_id', '$doctor_user_id', '$content', NOW())";

            // run insert
            if (mysqli_query($conn, $sqlInsert)) {
                // add notification for doctor
                mysqli_query($conn, "INSERT INTO notifications (user_id, type, created_at, message, status)
                                     VALUES ('$doctor_user_id', 'message', NOW(), 'You received a new message from a patient.', 'unread')");

                $message = "Message sent successfully";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        } else {
            $message = "Doctor not found";
        }
    }
}

// select all doctors
$sqlDoctors = "SELECT doctors.doctor_id, doctors.user_id, users.name, users.email, doctors.specialization
               FROM doctors
               JOIN users ON doctors.user_id = users.user_id
               ORDER BY users.name ASC";
$resultDoctors = mysqli_query($conn, $sqlDoctors);

// selected doctor info
$doctor = null;

// if doctor selected
if ($doctor_id != "") {
    // get selected doctor info
    $sqlDoctor = "SELECT doctors.doctor_id, doctors.user_id, users.name, users.email, doctors.specialization
                  FROM doctors
                  JOIN users ON doctors.user_id = users.user_id
                  WHERE doctors.doctor_id='$doctor_id'";
    $resultDoctor = mysqli_query($conn, $sqlDoctor);
    $doctor = mysqli_fetch_assoc($resultDoctor);

    // if doctor exists
    if ($doctor) {
        // get doctor user id
        $doctor_user_id = $doctor['user_id'];

        // mark patient message notifications as read
        mysqli_query($conn, "UPDATE notifications
                             SET status='read'
                             WHERE user_id='$patient_id'
                             AND type='message'
                             AND status='unread'");

        // get conversation
        $sqlMessages = "SELECT * FROM messages
                        WHERE (sender_id='$patient_id' AND receiver_id='$doctor_user_id')
                           OR (sender_id='$doctor_user_id' AND receiver_id='$patient_id')
                        ORDER BY timestamp ASC";
        $resultMessages = mysqli_query($conn, $sqlMessages);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Messages</title>
    <link rel="stylesheet" href="/HealthGuard/style.css">
</head>
<body class="patient-page">

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Messages</h1>
    <p class="section-subtitle">Send messages to doctors and view replies</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/patient/patient_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>
</div>

<div class="chat-layout">

    <div class="chat-sidebar">
        <h3 style="margin-bottom:16px;">Doctors</h3>

        <?php while ($rowDoctor = mysqli_fetch_assoc($resultDoctors)) { ?>
            <a class="doctor-item <?php if ($doctor_id == $rowDoctor['doctor_id']) echo 'active'; ?>"
               href="/HealthGuard/patient/my_messages.php?doctor_id=<?php echo $rowDoctor['doctor_id']; ?>">
                <strong>Dr. <?php echo $rowDoctor['name']; ?></strong><br>
                <small><?php echo $rowDoctor['specialization']; ?></small><br>
                <small><?php echo $rowDoctor['email']; ?></small>
            </a>
        <?php } ?>
    </div>

    <div class="chat-main">
        <?php if (!empty($message)) { ?>
            <div class="message" style="margin-bottom:15px;"><?php echo $message; ?></div>
        <?php } ?>

        <?php if ($doctor) { ?>
            <h3 style="margin-bottom:8px;">Chat with Dr. <?php echo $doctor['name']; ?></h3>
            <p style="margin-bottom:16px; color:#666;"><?php echo $doctor['specialization']; ?> - <?php echo $doctor['email']; ?></p>

            <div class="chat-box">
                <?php if (mysqli_num_rows($resultMessages) > 0) { ?>
                    <?php while ($rowMsg = mysqli_fetch_assoc($resultMessages)) { ?>
                        <div class="msg <?php if ($rowMsg['sender_id'] == $patient_id) echo 'msg-patient'; else echo 'msg-doctor'; ?>">
                            <?php echo nl2br($rowMsg['content']); ?>
                            <span class="msg-time"><?php echo $rowMsg['timestamp']; ?></span>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>No messages yet. Start the conversation.</p>
                <?php } ?>
            </div>

            <form class="chat-form" method="POST">
                <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">
                <textarea name="content" placeholder="Write your message here..."></textarea>
                <button type="submit" class="primary-btn">Send Message</button>
            </form>
        <?php } else { ?>
            <div class="card">
                <h3>Select a doctor</h3>
                <p>Choose a doctor from the left side to start chatting.</p>
            </div>
        <?php } ?>
    </div>

</div>

</body>
</html>
<?php
// close DB connection
CloseCon($conn);
?>