<!-- Berker Korkut -->
<!-- 20230702016 -->

<?php

// Load text datasets
$names = file("first_names.txt");
$categories = file("categories.txt");
$video_titles = file("video_titles.txt");
$video_urls = file("video_urls.txt");
$comment_bodies = file("comment_bodies.txt");

$file = fopen("seed.sql", "w");

// Generate random users
for($i = 1; $i <= 100; $i++){

    $random_index = rand(0, count($names)-1);

    $name = trim($names[$random_index]);

    $username = strtolower($name) . rand(10, 99);
    $password = "1234";
    $email = $username . "@gmail.com";
    $country = "Turkey";
    $bio = "Hello, I am " . $name;
    $image = "https://picsum.photos/200?random=" . rand(1,10000);

    $sql = "INSERT INTO USERS 
    (username, password, user_image, full_name, email, country, joined_on, bio)
    VALUES
    (
        '$username',
        '$password',
        '$image',
        '$name',
        '$email',
        '$country',
        CURDATE(),
        '$bio'
    );";

    fwrite($file, $sql);
}

// Generate random channels
for($i = 1; $i <= 50; $i++) {

    $random_index = rand(0, count($names) - 1);

    $name = trim($names[$random_index]);

    $category_index = rand(0, count($categories) - 1);

    $category = trim($categories[$category_index]);

    $channel_name = $name . " Channel";

    $channel_image = "https://picsum.photos/300?random=" . rand(1,10000);

    $description = "Welcome to " . $channel_name;

    $sql = "INSERT INTO CHANNELS
    (owner_id, channel_image, name, description, created_on, category)
    VALUES
    (
        $i,
        '$channel_image',
        '$channel_name',
        '$description',
        CURDATE(),
        '$category'
    );

";

    fwrite($file, $sql);
}

// Generate random videos
for($i = 1; $i <= 200; $i++) {

    $title_index = rand(0, count($video_titles) - 1);

    $title = trim($video_titles[$title_index]);

    $description = "This is a video about " . $title;

    $channel_id = rand(1, 50);

    $url_index = rand(0, count($video_urls) - 1);

    $url = trim($video_urls[$url_index]);

    $thumbnail = "https://picsum.photos/640/360?random=" . rand(1,10000);

    // Determine video duration from URL
    if(str_contains($url, "5s")) {
        $duration = 5;
    }
    else if(str_contains($url, "10s")) {
        $duration = 10;
    }
    else if(str_contains($url, "15s")) {
        $duration = 15;
    }
    else if(str_contains($url, "20s")) {
        $duration = 20;
    }
    else if(str_contains($url, "30s")) {
        $duration = 30;
    }
    else {
        $duration = 0;
    }  

    $views = rand(0, 100000);

    $likes = rand(0, 10000);

    $sql = "INSERT INTO VIDEOS
    (channel_id, title, description, url, thumbnail_url, duration_seconds, uploaded_at, view_count, like_count)
    VALUES
    (
        $channel_id,
        '$title',
        '$description',
        '$url',
        '$thumbnail',
        $duration,
        NOW(),
        $views,
        $likes
    );

";

    fwrite($file, $sql);
}

// Generate random subscriptions
for($i = 1; $i <= 300; $i++) {

    // Random user
    $subscriber_id =
    rand(1, 100);

    // Random channel
    $channel_id =
    rand(1, 50);

    // Prevent subscribing to own channel
    $owner_id = $channel_id;
    if($subscriber_id == $owner_id) {

        continue;

    }

    $sql = "INSERT IGNORE INTO SUBSCRIPTIONS

    (
        subscriber_id,
        channel_id,
        subscribed_at
    )

    VALUES

    (
        $subscriber_id,
        $channel_id,
        NOW()
    );

";

    fwrite($file, $sql);
}

// Generate top-level comments
for($i = 1; $i <= 130; $i++) {

    $video_id = rand(1, 200);

    $user_id = rand(1, 100);

    $comment_index =
    rand(0, count($comment_bodies) - 1);

    $body =
    trim($comment_bodies[$comment_index]);

    $sql = "INSERT INTO COMMENTS
    (video_id, user_id, parent_comment_id, body, posted_at)
    VALUES
    (
        $video_id,
        $user_id,
        NULL,
        '$body',
        NOW()
    );

";

    fwrite($file, $sql);
}

// Generate reply comments
for($i = 1; $i <= 30; $i++) {

    // Select random parent comment
    $parent_comment_id = rand(1, 130);

    // Replies should belong to the same video as parent comment
    fwrite(
        $file,

        "SET @video_id = (
            SELECT video_id
            FROM COMMENTS
            WHERE comment_id =
            $parent_comment_id
        );\n"
    );

    // Random reply owner
    $user_id = rand(1, 100);

    

    $comment_index =
    rand(0, count($comment_bodies) - 1);

    $body =
    "Reply: " .
    trim($comment_bodies[$comment_index]);

    $sql = "INSERT INTO COMMENTS
    (video_id, user_id, parent_comment_id, body, posted_at)
    VALUES
    (
        @video_id,
        $user_id,
        $parent_comment_id,
        '$body',
        NOW()
    );

";

    fwrite($file, $sql);
}

// Close seed.sql file
fclose($file);

// Display success message
echo "seed.sql created successfully";

?>