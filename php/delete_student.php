<?php
// Database connection details
$host = 'localhost';
$dbname = 'robotic course management';
$user = 'root';  // Replace with your MySQL username
$pass = '';      // Replace with your MySQL password

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
require "functions.php";

checkSessionTimeout();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 3 ) { // Only Admin can delete
    header("Location: login.php");
    exit("Unauthorized access.");
}

$user_role = (int)$_SESSION['role']; // Cast to integer to match type
$user_id = $_SESSION['id'];

//Generate a CSRF token
$csrf_token= bin2hex(random_bytes(32));
$csrf_token_hashed= hash("sha256", $csrf_token);
$issued_at = date("Y-m-d H:i:s");
$expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

//Store the token in the database
$admin_id = ($user_role === 3) ? $user_id : null;

$stmt = $conn->prepare("INSERT INTO csrf (TOKEN, ISSUED_AT, EXPIRES_AT, ADMIN_ID, STAFF_ID) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssii", $csrf_token_hashed, $issued_at, $expires_at, $admin_id, $staff_id);

if (!$stmt->execute()){
    die("Error inserting CSRF token: " . $stmt->error);
}
$stmt->close();

$_SESSION['csrf_token'] = $csrf_token;
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $submitted_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$submitted_token || hash("sha256", $submitted_token)!== $csrf_token_hashed){
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        exit("Invalid CSRF token.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])){
    $student_id = $_POST['id'];

    
    $stmt = $conn -> prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("s", $student_id);

    if ($stmt->execute()){
        echo "Record deleted successfully";
        header("Location: student_records.php");
        exit();
    }else{
        echo "Error deleting record" . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>