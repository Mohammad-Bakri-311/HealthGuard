<?php
// start session
session_start();

// include DB connection
include("../db_connection.php");

// check if doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "doctor") {
    // redirect to login
    header("Location: /HealthGuard/login.php");
    // stop script
    exit();
}

// open DB connection
$conn = OpenCon();

// get doctor user id
$doctor_user_id = $_SESSION['user_id'];

// message variable
$message = "";

// selected patient id
$patient_id = "";

// check if patient selected
if (isset($_GET['patient_id'])) {
    // get patient id
    $patient_id = $_GET['patient_id'];
}

// send reply
if (isset($_POST['patient_id']) && isset($_POST['content'])) {
    // get patient id
    $patient_id = $_POST['patient_id'];

    // get message text
    $content = trim($_POST['content']);

    // check empty
    if ($content == "") {
        $message = "Please write a message";
    } else {
        // insert reply
        $sqlInsert = "INSERT INTO messages (sender_id, receiver_id, content, timestamp)
                      VALUES ('$doctor_user_id', '$patient_id', '$content', NOW())";

        // run insert
        if (mysqli_query($conn, $sqlInsert)) {
            // add notification for patient
            mysqli_query($conn, "INSERT INTO notifications (user_id, type, created_at, message, status)
                                 VALUES ('$patient_id', 'message', NOW(), 'You received a new reply from your doctor.', 'unread')");

            $message = "Reply sent successfully";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

// select patients who talked with doctor
$sqlPatients = "SELECT DISTINCT users.user_id, users.name, users.email
                FROM messages
                JOIN users ON (
                    (messages.sender_id = users.user_id AND messages.receiver_id = '$doctor_user_id')
                    OR
                    (messages.receiver_id = users.user_id AND messages.sender_id = '$doctor_user_id')
                )
                WHERE users.role = 'patient'
                ORDER BY users.name ASC";
$resultPatients = mysqli_query($conn, $sqlPatients);

// selected patient info
$patient = null;

// if patient selected
if ($patient_id != "") {
    // get patient info
    $sqlPatient = "SELECT user_id, name, email
                   FROM users
                   WHERE user_id='$patient_id' AND role='patient'";
    $resultPatient = mysqli_query($conn, $sqlPatient);
    $patient = mysqli_fetch_assoc($resultPatient);

    if ($patient) {
        // mark doctor message notifications as read
        mysqli_query($conn, "UPDATE notifications
                             SET status='read'
                             WHERE user_id='$doctor_user_id'
                             AND type='message'
                             AND status='unread'");

        // get conversation
        $sqlMessages = "SELECT * FROM messages
                        WHERE (sender_id='$doctor_user_id' AND receiver_id='$patient_id')
                           OR (sender_id='$patient_id' AND receiver_id='$doctor_user_id')
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
    <style>
        .chat-layout{
            max-width:1200px;
            margin:30px auto;
            display:grid;
            grid-template-columns:320px 1fr;
            gap:24px;
            padding:0 20px;
        }
        .chat-sidebar,.chat-main{
            background:#fff;
            border-radius:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.08);
            padding:20px;
        }
        .patient-item{
            display:block;
            text-decoration:none;
            color:#222;
            border:1px solid #eee;
            border-radius:16px;
            padding:14px;
            margin-bottom:12px;
            transition:0.2s;
        }
        .patient-item:hover{
            transform:translateY(-2px);
        }
        .patient-item.active{
            border:2px solid #222;
        }
        .chat-box{
            height:420px;
            overflow-y:auto;
            border:1px solid #eee;
            border-radius:16px;
            padding:16px;
            background:#fafafa;
            margin-bottom:16px;
        }
        .msg{
            max-width:70%;
            padding:12px 14px;
            border-radius:16px;
            margin-bottom:12px;
            word-wrap:break-word;
        }
        .msg-doctor{
            margin-left:auto;
            background:#d9f4ff;
        }
        .msg-patient{
            margin-right:auto;
            background:#eeeeee;
        }
        .msg-time{
            display:block;
            font-size:12px;
            margin-top:6px;
            color:#666;
        }
        .chat-form textarea{
            width:100%;
            min-height:100px;
            border:1px solid #ddd;
            border-radius:14px;
            padding:12px;
            resize:vertical;
            margin-bottom:12px;
            font-family:inherit;
        }
    </style>
</head>
<body>

<?php include("../navbar.php"); ?>

<div class="section">
    <h1 class="section-title">My Messages</h1>
    <p class="section-subtitle">Reply to patient messages</p>

    <div class="hero-actions" style="justify-content:center; margin-bottom:25px;">
        <a href="/HealthGuard/doctor/doctor_dashboard.php" class="secondary-btn">Back to Dashboard</a>
    </div>
</div>

<div class="chat-layout">

    <div class="chat-sidebar">
        <h3 style="margin-bottom:16px;">Patients</h3>

        <?php if (mysqli_num_rows($resultPatients) > 0) { ?>
            <?php while ($rowPatient = mysqli_fetch_assoc($resultPatients)) { ?>
                <a class="patient-item <?php if ($patient_id == $rowPatient['user_id']) echo 'active'; ?>"
                   href="/HealthGuard/doctor/my_messages.php?patient_id=<?php echo $rowPatient['user_id']; ?>">
                    <strong><?php echo $rowPatient['name']; ?></strong><br>
                    <small><?php echo $rowPatient['email']; ?></small>
                </a>
            <?php } ?>
        <?php } else { ?>
            <p>No patient messages yet.</p>
        <?php } ?>
    </div>

    <div class="chat-main">
        <?php if (!empty($message)) { ?>
            <div class="message" style="margin-bottom:15px;"><?php echo $message; ?></div>
        <?php } ?>

        <?php if ($patient) { ?>
            <h3 style="margin-bottom:8px;">Chat with <?php echo $patient['name']; ?></h3>
            <p style="margin-bottom:16px; color:#666;"><?php echo $patient['email']; ?></p>

            <div class="chat-box">
                <?php if (mysqli_num_rows($resultMessages) > 0) { ?>
                    <?php while ($rowMsg = mysqli_fetch_assoc($resultMessages)) { ?>
                        <div class="msg <?php if ($rowMsg['sender_id'] == $doctor_user_id) echo 'msg-doctor'; else echo 'msg-patient'; ?>">
                            <?php echo nl2br($rowMsg['content']); ?>
                            <span class="msg-time"><?php echo $rowMsg['timestamp']; ?></span>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>No messages yet.</p>
                <?php } ?>
            </div>

            <form class="chat-form" method="POST">
                <input type="hidden" name="patient_id" value="<?php echo $patient['user_id']; ?>">
                <textarea name="content" placeholder="Write your reply here..."></textarea>
                <button type="submit" class="primary-btn">Send Reply</button>
            </form>
        <?php } else { ?>
            <div class="card">
                <h3>Select a patient</h3>
                <p>Choose a patient from the left side to open the conversation.</p>
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