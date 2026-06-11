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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: "
    . $conn->connect_error);
}

// Check whether channel_id exists
if(!isset($_GET['channel_id'])) {
    die("Channel ID not found.");
}

// Get channel and user IDs from URL
$channel_id = $_GET['channel_id'];
$user_id = $_GET['user_id'];

// Retrieve channel information and subscriber count
$channel_sql = "
SELECT CHANNELS.*,
USERS.full_name,
USERS.country,

(
    SELECT COUNT(*)
    FROM SUBSCRIPTIONS
    WHERE SUBSCRIPTIONS.channel_id =
    CHANNELS.channel_id
)
AS subscriber_count

FROM CHANNELS

JOIN USERS
ON CHANNELS.owner_id =
USERS.user_id

WHERE CHANNELS.channel_id =
$channel_id
";

$channel_result =
$conn->query($channel_sql);

$channel =
$channel_result->fetch_assoc();

// Check whether channel exists
if(!$channel) {
    die("Channel not found.");
}

// Check if current user is subscribed
$subscription_sql = "
SELECT *
FROM SUBSCRIPTIONS
WHERE subscriber_id = $user_id
AND channel_id = $channel_id
";

$subscription_result =
$conn->query($subscription_sql);

$is_subscribed =
$subscription_result->num_rows > 0;

// Retrieve videos uploaded by the channel
$video_sql = "
SELECT *
FROM VIDEOS
WHERE channel_id = $channel_id
ORDER BY uploaded_at DESC
";

$video_result =
$conn->query($video_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
    content="width=device-width,
    initial-scale=1.0">

    <title>
        <?php echo $channel['name']; ?>
    </title>
</head>
<body>

<a href="feed.php?user_id=
<?php echo $user_id; ?>">
    Back to Feed
</a>

<hr>

<h1>
    <?php echo $channel['name']; ?>
</h1>

<img
src="<?php echo $channel['channel_image']; ?>"
width="200">

<p>
Category:
<?php echo $channel['category']; ?>
</p>

<p>
Owner:
<?php echo $channel['full_name']; ?>
</p>

<p>
Country:
<?php echo $channel['country']; ?>
</p>

<p>
Subscribers:
<?php echo $channel['subscriber_count']; ?>
</p>

<p>
Created:
<?php echo $channel['created_on']; ?>
</p>

<p>

<?php

if($channel['description']) {

    echo $channel['description'];

}
else {

    echo "(no description)";
}

?>

</p>

<?php

if($is_subscribed) {

?>

<a href="
unsubscribe.php?
user_id=<?php echo $user_id; ?>
&channel_id=<?php echo $channel_id; ?>
&video_id=0
">
    Unsubscribe
</a>

<?php

}
else {

?>

<a href="
subscribe.php?
user_id=<?php echo $user_id; ?>
&channel_id=<?php echo $channel_id; ?>
&video_id=0
">
    Subscribe
</a>

<?php
}
?>

<hr>

<h2>Channel Videos</h2>

<?php

while($video =
$video_result->fetch_assoc()) {

    echo "<div>";

    echo "<h3>";

    echo "<a href='watch.php?video_id="
    . $video['video_id']
    . "&user_id="
    . $user_id .
    "'>";

    echo $video['title'];

    echo "</a>";

    echo "</h3>";

    echo "<p>";

    echo "Duration: "
    . $video['duration_seconds']
    . " seconds";

    echo "</p>";

    echo "<p>";

    echo "Views: "
    . $video['view_count'];

    echo "</p>";

    echo "<p>";

    echo "Uploaded: "
    . $video['uploaded_at'];

    echo "</p>";

    echo "<hr>";

    echo "</div>";
}

?>

<?php

?>