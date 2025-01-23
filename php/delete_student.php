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
if (!isset($_SESSION['username']) || $_SESSION['role'] != 3) { // Only Admin can delete
    header("Location: login.php");
    exit();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])){
    $student_id = $_POST['id'];

    
    $stmt = $conn -> prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()){
        echo "Record deleted successfully";
    }else{
        echo "Error deleting record" . $conn->error;
    }
    $stmt->close();
}
}
$conn->close();

header("Location: student_records.php");
exit;
?>