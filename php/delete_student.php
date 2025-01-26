<?php
// Database connection details
$host = 'localhost';
$dbname = 'robotic course management';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 3) { // Only Admin can delete
    header("Location: login.php");
    exit("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $student_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $submitted_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);

    // Validate CSRF token
    if (!$submitted_token || $submitted_token !== $_SESSION['csrf_token']) {
        http_response_code(403);
        exit("Invalid CSRF token.");
    }

    if ($student_id) {
        // Delete the student
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("s", $student_id);

        if ($stmt->execute()) {
            echo "Record deleted successfully!";
            header("Location: student_records.php");
            exit();
        } else {
            echo "Error deleting record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Invalid student ID.";
    }
}

$conn->close();
?>
