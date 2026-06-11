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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check whether login form was submitted
if(!isset($_POST['username']) || !isset($_POST['password'])) {
    die("Please login first.");
}

// Retrieve username and password entered by the user
$user_username = $_POST['username'];
$user_password = $_POST['password'];

// Query matching user credentials
$sql = "SELECT * FROM USERS
        WHERE username='$user_username'
        AND password='$user_password'";

// Execute login query
$result = $conn->query($sql);

// Check whether authentication is successful
if($result->num_rows > 0) {

    // Retrieve authenticated user data
    $row = $result->fetch_assoc();

    // Redirect user to homepage
    header(
    "Location: feed.php?user_id="
    . $row['user_id']
    );

    exit();

} else {

    // Display login error message
    echo "Invalid username or password";
}

?>