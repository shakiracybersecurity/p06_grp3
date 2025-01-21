<?php
function db_connect(){
    $host = 'localhost';
$dbname = 'robotic course management'; // Updated for clarity
$user = 'root';
$pass = '';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
}

// check if the user is logged in and role of user
function is_logged_in($allowed) {
    if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}elseif(!in_array($_SESSION['role'], $allowed)){
    header("Location: login.php");
    exit();
}}

?>
