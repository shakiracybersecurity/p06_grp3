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

// Ensure only Admins can access
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit("Unauthorized access.");
}

// Role check for Admin access
if ($_SESSION['role'] != 3) { // Role 3 is Admin
    echo "Unauthorized access. Only admins can delete records.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $student_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $submitted_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);

    // Validate CSRF token
    if (!$submitted_token || $submitted_token !== $_SESSION['csrf_token']) {
        http_response_code(403);
        exit("Invalid CSRF token.");
    }

    if ($student_id) {
        // Delete related records from student_courses table
        $stmt_courses = $conn->prepare("DELETE FROM student_courses WHERE student_id = ?");
        $stmt_courses->bind_param("i", $student_id);
        if (!$stmt_courses->execute()) {
            echo "Error deleting related courses: " . $stmt_courses->error;
            $stmt_courses->close();
            exit();
        }
        $stmt_courses->close();

        // Delete the student record
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        if ($stmt->execute()) {
            // Display success message and redirect
            echo "<script>alert('Record deleted successfully!'); window.location.href = 'read1.php';</script>";
            exit();
        } else {
            echo "Error deleting student: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Invalid student ID.";
    }
}

$conn->close();
?>
