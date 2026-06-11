<!-- Berker Korkut -->
<!-- 20230702016 -->

<?php

// Database connection information
$servername = "localhost";
$username = "root";
$password = "mysql";
$database = "BERKER_KORKUT";

// Create database connection
$conn = new mysqli(
    $servername,
    $username,
    $password,
    $database
);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user, channel and video IDs from URL parameters
$user_id = $_GET['user_id'];

$channel_id = $_GET['channel_id'];

$video_id = $_GET['video_id'];

// Insert new subscription record
// IGNORE prevents duplicate subscriptions
$sql = "
INSERT IGNORE INTO SUBSCRIPTIONS
(subscriber_id, channel_id, subscribed_at)
VALUES
(
    $user_id,
    $channel_id,
    NOW()
)
";

// Execute subscription query
$conn->query($sql);

// Redirect user back to video page
header(
"Location: watch.php?video_id=$video_id&user_id=$user_id"
);

exit();

?>