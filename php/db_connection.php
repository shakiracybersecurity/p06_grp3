<?php
// Database connection using mysqli
function db_connect() {
    $host = 'localhost';
    $dbname = 'robotic course management';
    $user = 'root';
    $pass = '';

    // Enable error reporting for debugging
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Create connection
    $conn = new mysqli($host, $user, $pass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("An error occurred while connecting to the database.");
    }

    return $conn;
}
?>
