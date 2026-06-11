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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Character encoding -->
    <meta charset="UTF-8">

    <!-- Responsive page settings -->
    <meta name="viewport"
    content="width=device-width,
    initial-scale=1.0">

    <!-- Page title -->
    <title>SQL Console</title>
</head>
<body>

<!-- SQL console heading -->
<h1>SQL Console</h1>

<!-- SQL query form -->
<form method="POST">

    <!-- SQL input area -->
    <textarea
    name="sql_query"
    rows="10"
    cols="80"
    required>
    </textarea>

    <br><br>

    <!-- Execute SQL query -->
    <button type="submit">
        Run Query
    </button>

</form>

<hr>

<?php

// Check whether a SQL query was submitted
if(isset($_POST['sql_query'])) {

    // Retrieve SQL query entered by user
    $sql =
    $_POST['sql_query'];

    // Execute SQL query
    $result =
    $conn->query($sql);

    // Check if query executed successfully
    if($result === TRUE) {

        echo "Query executed successfully.";

    }
    // Check if query returned rows
    else if($result instanceof mysqli_result) {

        echo "<h3>Executed Query:</h3>";
        echo "<pre>$sql</pre>";

        // Create HTML table for query results
        echo "<table border='1'
        cellpadding='10'>";

        echo "<tr>";

        // Display table column names
        while($field =
        $result->fetch_field()) {

            echo "<th>"
            . $field->name .
            "</th>";
        }

        echo "</tr>";

        // Display query result rows
                while($row =
        $result->fetch_assoc()) {

            echo "<tr>";

            // Display row values
            foreach($row as $value) {

                echo "<td>"
                . $value .
                "</td>";
            }

            echo "</tr>";
        }

        echo "</table>";
    }

    // Display SQL error message
    else {

        echo "Error: "
        . $conn->error;
    }
}

?>

</body>
</html>