<!-- Berker Korkut -->
<!-- 20230702016 -->

<?php
// This is a reference PHP code for creating and filling the database.
// Add new lines and queries based on your given project.

// Database server information
$servername = "localhost";
$username = "root";
$password = "mysql";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$drop_sql = "DROP DATABASE IF EXISTS BERKER_KORKUT";

if ($conn->query($drop_sql) === FALSE) {
    die("Error deleting old database: " . $conn->error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS BERKER_KORKUT";

// For checking given sql query is executed correctly
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

//Select database
mysqli_select_db($conn, 'BERKER_KORKUT');

// Create tables
$sql = "CREATE TABLE IF NOT EXISTS USERS (user_id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(255) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, user_image VARCHAR(255), full_name VARCHAR(255), email VARCHAR(255), country VARCHAR(255), joined_on DATE, bio TEXT);
        CREATE TABLE IF NOT EXISTS CHANNELS (channel_id INT PRIMARY KEY AUTO_INCREMENT, owner_id INT UNIQUE, channel_image VARCHAR(255), name VARCHAR(255), description TEXT, created_on DATE, category VARCHAR(255), FOREIGN KEY (owner_id) REFERENCES USERS(user_id) ON DELETE CASCADE);
        CREATE TABLE IF NOT EXISTS VIDEOS (video_id INT PRIMARY KEY AUTO_INCREMENT, channel_id INT, title VARCHAR(255), description TEXT, url VARCHAR(255), thumbnail_url VARCHAR(255), duration_seconds INT, uploaded_at DATETIME, view_count INT, like_count INT, FOREIGN KEY (channel_id) REFERENCES CHANNELS(channel_id) ON DELETE CASCADE);
        CREATE TABLE IF NOT EXISTS SUBSCRIPTIONS (subscription_id INT PRIMARY KEY AUTO_INCREMENT, subscriber_id INT, channel_id INT, subscribed_at DATETIME, UNIQUE(subscriber_id, channel_id), FOREIGN KEY (subscriber_id) REFERENCES USERS(user_id) ON DELETE CASCADE, FOREIGN KEY (channel_id) REFERENCES CHANNELS(channel_id) ON DELETE CASCADE);
        CREATE TABLE IF NOT EXISTS COMMENTS (comment_id INT PRIMARY KEY AUTO_INCREMENT, video_id INT, user_id INT, parent_comment_id INT, body TEXT, posted_at DATETIME, FOREIGN KEY (video_id) REFERENCES VIDEOS(video_id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES USERS(user_id) ON DELETE CASCADE, FOREIGN KEY (parent_comment_id) REFERENCES COMMENTS(comment_id) ON DELETE SET NULL);
        ";

// For checking given sql query is executed correctly
if($conn->multi_query($sql) === FALSE) {
    die("Error creating tables: " . $conn->error);
}

// Clear remaining query results
while ($conn->next_result()) {;}

// Fill tables
// ...

// Generate fresh seed file
include("generate_data.php");

// Read generated SQL seed file
$seed_file = file_get_contents("seed.sql");

// Insert generated seed data into database
if ($conn->multi_query($seed_file) === TRUE) {
   // echo "Seed data inserted successfully<br>";
} else {
    die("Error inserting seed data: " . $conn->error);
}

header("Location: login.html");

exit();

?>
