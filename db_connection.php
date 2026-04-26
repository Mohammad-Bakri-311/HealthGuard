<?php
function OpenCon()
{
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "1234";
    $db = "healthguard";

    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $db);

    if (!$conn) {
        die("Connect failed: " . mysqli_connect_error());
    }

    return $conn;
}

function CloseCon($conn)
{
    mysqli_close($conn);
}
?>