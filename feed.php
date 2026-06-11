<!-- Berker Korkut -->
<!-- 20230702016 -->

<?php

// Database connection information
$servername = "localhost";
$username = "root";
$password = "mysql";
$database = "BERKER_KORKUT";

// Create database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check whether user_id exists
if(!isset($_GET['user_id'])) {
    die("User ID not found.");
}

// Get current user ID
$user_id = $_GET['user_id'];

// Retrieve current user information
$user_sql = "SELECT * FROM USERS WHERE user_id = $user_id";

$user_result = $conn->query($user_sql);

$user = $user_result->fetch_assoc();

// Retrieve latest videos from subscribed channels
$subscribed_sql = "
SELECT VIDEOS.*,

CHANNELS.name AS channel_name,
CHANNELS.channel_image,
USERS.country AS uploader_country,

DATEDIFF(
NOW(),
uploaded_at
) AS days_ago
FROM VIDEOS

JOIN CHANNELS
ON VIDEOS.channel_id = CHANNELS.channel_id

JOIN USERS
ON CHANNELS.owner_id = USERS.user_id

JOIN SUBSCRIPTIONS
ON CHANNELS.channel_id = SUBSCRIPTIONS.channel_id

WHERE SUBSCRIPTIONS.subscriber_id = $user_id

ORDER BY uploaded_at DESC

LIMIT 5
";

$subscribed_result =
$conn->query($subscribed_sql);

// Retrieve recommended videos from unsubscribed channels
$general_sql = "
SELECT VIDEOS.*,

CHANNELS.name AS channel_name,
CHANNELS.channel_image,
USERS.country AS uploader_country,

DATEDIFF(
NOW(),
uploaded_at
) AS days_ago
FROM VIDEOS

JOIN CHANNELS
ON VIDEOS.channel_id = CHANNELS.channel_id

JOIN USERS
ON CHANNELS.owner_id = USERS.user_id

WHERE VIDEOS.channel_id NOT IN (

    SELECT channel_id
    FROM SUBSCRIPTIONS
    WHERE subscriber_id = $user_id

)

ORDER BY uploaded_at DESC

LIMIT 5
";

$general_result =
$conn->query($general_sql);

// Retrieve the top 5 channels by subscriber count
$top_channels_sql = "

SELECT CHANNELS.*,

COUNT(SUBSCRIPTIONS.subscription_id)
AS subscriber_count

FROM CHANNELS

LEFT JOIN SUBSCRIPTIONS
ON CHANNELS.channel_id =
SUBSCRIPTIONS.channel_id

GROUP BY CHANNELS.channel_id

ORDER BY subscriber_count DESC

LIMIT 5
";

$top_channels_result =
$conn->query($top_channels_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed</title>
</head>
<body>

<style>

body {

    font-family: Arial;
    margin: 20px;

}

.container {

    display: flex;
    gap: 30px;

}

.left-section {

    width: 70%;

}

.right-section {

    width: 30%;

}

.box {

    border: 1px solid #ccc;
    padding: 15px;
    margin-bottom: 20px;

}

</style>

    <!-- Welcome message -->
    <h1>
    Hello, <?php echo $user['full_name']; ?>!
    </h1>

    <hr>

    <div class="container">

    <div class="left-section">

    <hr>

    <!-- Latest videos from subscribed channels -->
    <h2>Subscribed Videos</h2>

<?php

while($video =
$subscribed_result->fetch_assoc()) {

    echo "<div>";

    // Display video thumbnail
    echo "<img
    src='" . $video['thumbnail_url'] . "'
    width='320'>";

    echo "<a href='channel.php?channel_id="
    . $video['channel_id']
    . "&user_id="
    . $user_id .
    "'>";

    echo "<img
    src='" . $video['channel_image'] . "'
    width='80'>";

    echo "</a>";

    echo "<h3>";

    echo "<a href='watch.php?video_id="
    . $video['video_id']
    . "&user_id="
    . $user_id .
    "'>";

    echo $video['title'];

    echo "</a>";

    echo "</h3>";

    // Redirect user to channel page
    echo "<p>Channel:
    <a href='channel.php?channel_id="
    . $video['channel_id']
    . "&user_id="
    . $user_id .
    "'>"
    . $video['channel_name']
    . "</a>
    </p>";

    // Display uploader country
    echo "<p>Country: "
    . $video['uploader_country']
    . "</p>";

    // Display upload date difference
    echo "<p>Uploaded "
    . $video['days_ago']
    . " days ago</p>";

    // Display video statistics
    echo "<p>Views: "
    . $video['view_count']
    . "</p>";

    echo "<p>Likes: "
    . $video['like_count']
    . "</p>";

    // Display video duration
    echo "<p>Duration: "
    . $video['duration_seconds']
    . " seconds</p>";

    // Redirect user to watch page
    echo "<a href='watch.php?video_id="
    . $video['video_id']
    . "&user_id="
    . $user_id .
    "'>
    Watch Video
    </a>";

    echo "<hr>";

    echo "</div>";
}

?>

<!-- Recommended videos section -->
<h2>Recommended Videos</h2>

<?php

while($video =
$general_result->fetch_assoc()) {

    echo "<div>";

    // Display video thumbnail
    echo "<img
    src='" . $video['thumbnail_url'] . "'
    width='320'>";

    echo "<a href='channel.php?channel_id="
    . $video['channel_id']
    . "&user_id="
    . $user_id .
    "'>";

    echo "<img
    src='" . $video['channel_image'] . "'
    width='80'>";

    echo "</a>";

    echo "<h3>";

    echo "<a href='watch.php?video_id="
    . $video['video_id']
    . "&user_id="
    . $user_id .
    "'>";

    echo $video['title'];

    echo "</a>";

    echo "</h3>";

    // Redirect user to channel page
    echo "<p>Channel:
    <a href='channel.php?channel_id="
    . $video['channel_id']
    . "&user_id="
    . $user_id .
    "'>"
    . $video['channel_name']
    . "</a>
    </p>";

    // Display uploader country
    echo "<p>Country: "
    . $video['uploader_country']
    . "</p>";

    // Display upload date difference
    echo "<p>Uploaded "
    . $video['days_ago']
    . " days ago</p>";

    // Display video statistics
    echo "<p>Views: "
    . $video['view_count']
    . "</p>";

    echo "<p>Likes: "
    . $video['like_count']
    . "</p>";

    // Display video duration
    echo "<p>Duration: "
    . $video['duration_seconds']
    . " seconds</p>";

    // Redirect user to watch page
    echo "<a href='watch.php?video_id="
    . $video['video_id']
    . "&user_id="
    . $user_id .
    "'>
    Watch Video
    </a>";

    echo "<hr>";

    echo "</div>";
}

?>

</div>

<div class="right-section">

<div class="box">

<!-- Top channels section -->
<h2>Top Channels</h2>

<?php

while($channel =
$top_channels_result->fetch_assoc()) {

    echo "<div>";

    echo "<h3>";

    // Redirect user to selected channel page
    echo "<a href='channel.php?channel_id="
    . $channel['channel_id']
    . "&user_id="
    . $user_id .
    "'>";

    echo $channel['name'];

    echo "</a>";

    echo "</h3>";

    // Display subscriber count
    echo "<p>";

    echo "Subscribers: "
    . $channel['subscriber_count'];

    echo "</p>";

    echo "<hr>";

    echo "</div>";
}

?>

</div>

<div class="box">

<h2>User Profile</h2>

<p>
Full Name:
<?php echo $user['full_name']; ?>
</p>

<p>
Country:
<?php echo $user['country']; ?>
</p>

<p>
Email:
<?php echo $user['email']; ?>
</p>

<p>
Joined:
<?php echo $user['joined_on']; ?>
</p>

<p>
Bio:
<?php echo $user['bio']; ?>
</p>

</div>

</div>

</div>

</body>
</html>