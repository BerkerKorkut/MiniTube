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

// Check whether video_id exists
if(!isset($_GET['video_id'])) {
    die("Video ID not found.");
}

// Retrieve video ID
$video_id = $_GET['video_id'];

// Increment video view count
$increment_sql = "
UPDATE VIDEOS
SET view_count = view_count + 1
WHERE video_id = $video_id
";

$conn->query($increment_sql);

// Retrieve user ID
$user_id = $_GET['user_id'];

// Check whether comment form was submitted
if(
    isset($_POST['comment_body'])
) {

    // Retrieve comment text
    $comment_body =
    $_POST['comment_body'];

    // Insert new comment into database
    $insert_comment_sql = "
    INSERT INTO COMMENTS
    (
        video_id,
        user_id,
        parent_comment_id,
        body,
        posted_at
    )
    VALUES
    (
        $video_id,
        $user_id,
        NULL,
        '$comment_body',
        NOW()
    )
    ";

    // Execute comment insertion query
    $conn->query(
    $insert_comment_sql
    );

    // Reload page after comment insertion
    header(
    "Location: watch.php?video_id="
    . $video_id .
    "&user_id="
    . $user_id
    );

    exit();
}

// Retrieve video information
$sql = "
SELECT VIDEOS.*,

CHANNELS.name AS channel_name,

USERS.country AS uploader_country,

CASE

    WHEN view_count >= 1000
    THEN 'Popular'

    WHEN view_count >= 100
    THEN 'Trending'

    ELSE 'New'

END AS popularity_badge

FROM VIDEOS

JOIN CHANNELS
ON VIDEOS.channel_id =
CHANNELS.channel_id

JOIN USERS
ON CHANNELS.owner_id =
USERS.user_id

WHERE video_id = $video_id
";

$result = $conn->query($sql);

$video = $result->fetch_assoc();

// Check whether video exists
if(!$video) {
    die('Video not found.');
}

// Format video duration
$minutes =
floor($video['duration_seconds'] / 60);

$seconds =
$video['duration_seconds'] % 60;

$formatted_duration =
$minutes . ":" .
str_pad($seconds, 2, "0",
STR_PAD_LEFT);

// Retrieve channel ID
$channel_id = $video['channel_id'];

// Check whether current user is subscribed to the channel
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

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Character encoding -->
    <meta charset="UTF-8">

    <!-- Responsive page settings -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page title -->
    <title>Watch Video</title>

</head>
<body>

    <!-- Redirect user back to feed page -->
    <a href="feed.php?user_id=<?php echo $user_id; ?>">
        Back to Feed
    </a>

    <hr>

    <!-- Video title -->
    <h1><?php echo $video['title']; ?></h1>

    <!-- Channel information -->
    <p>
    Channel:

    <a href="
    channel.php?channel_id=<?php
    echo $channel_id;
    ?>&user_id=<?php
    echo $user_id;
    ?>">

    <?php echo $video['channel_name']; ?>

    </a>
    </p>
    
    <p>
    Country:
    <?php echo $video['uploader_country']; ?>
    </p>

    <br>

<?php

// Display subscribe/unsubscribe button
if($is_subscribed) {

?>

    <a href="
    unsubscribe.php?
    user_id=<?php echo $user_id; ?>
    &channel_id=<?php echo $channel_id; ?>
    &video_id=<?php echo $video_id; ?>
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
    &video_id=<?php echo $video_id; ?>
    ">
        Subscribe
    </a>

<?php

}

?>

    <!-- Video statistics -->
    <p>
        Views:
        <?php echo $video['view_count']; ?>
    </p>

    <p>
        Duration:
        <?php echo $formatted_duration; ?>
    </p>

    <p>
    Uploaded:
    <?php echo $video['uploaded_at']; ?>
    </p>

    <p>
        Likes:
        <?php echo $video['like_count']; ?>
    </p>

    <p>
        Badge:
        <?php echo $video['popularity_badge']; ?>
    </p>

    <!-- Video player -->
    <video width="640" controls>

        <source
            src="<?php echo $video['url']; ?>"
            type="video/mp4">

    </video>

    <!-- Video description -->
    <p>
        <?php echo $video['description']; ?>
    </p>

    <hr>

<!-- Comments section -->
<h2>Comments</h2>

<?php

// Retrieve comments and replies with self join
$comment_sql = "

SELECT

c1.comment_id AS parent_id,
c1.body AS parent_body,
c1.posted_at AS parent_posted_at,
u1.username AS parent_username,

c2.comment_id AS reply_id,
c2.body AS reply_body,
c2.posted_at AS reply_posted_at,
u2.username AS reply_username

FROM COMMENTS c1

JOIN USERS u1
ON c1.user_id = u1.user_id

LEFT JOIN COMMENTS c2
ON c1.comment_id = c2.parent_comment_id

LEFT JOIN USERS u2
ON c2.user_id = u2.user_id

WHERE c1.video_id = $video_id
AND c1.parent_comment_id IS NULL

ORDER BY c1.posted_at DESC,
c2.posted_at ASC
";

$comment_result =
$conn->query($comment_sql);

$current_parent = -1;

// Display comments and replies
while($row =
$comment_result->fetch_assoc()) {

    // New parent comment
    if($current_parent !=
    $row['parent_id']) {

        // Close previous parent block
        if($current_parent != -1) {

            echo "<hr>";
            echo "</div>";

        }

        echo "<div>";

        echo "<h4>"
        . $row['parent_username']
        . "</h4>";

        echo "<small>"
        . $row['parent_posted_at']
        . "</small>";

        echo "<p>"
        . $row['parent_body']
        . "</p>";

            $current_parent =
            $row['parent_id'];
        }

    // Display reply if exists
    if($row['reply_id']) {

        echo "<div style='margin-left:40px;'>";

        echo "<h5>"
        . $row['reply_username']
        . "</h5>";

        echo "<small>"
        . $row['reply_posted_at']
        . "</small>";

        echo "<p>"
        . $row['reply_body']
        . "</p>";

        echo "</div>";
    }
}

// Close last parent block
if($current_parent != -1) {

    echo "<hr>";
    echo "</div>";

}

?>

<hr>

<!-- Add comment section -->
<h2>Add Comment</h2>

<!-- Comment submission form -->
<form method="POST">

    <textarea
    name="comment_body"
    rows="4"
    cols="50"
    required>
    </textarea>

    <br><br>

    <!-- Submit comment -->
    <button type="submit">
        Post Comment
    </button>

</form>

</body>
</html>