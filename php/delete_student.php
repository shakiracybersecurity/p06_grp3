<?php
// Database connection details
require 'functions.php';
$conn = db_connect();
// Start session
session_start();
is_logged_in([3]);

checkSessionTimeout();


// Role check for Admin access
if ($_SESSION['role'] != 3) { // Role 3 is Admin
    echo "<script>
            alert('Unauthorized access. Only admins can delete records.');
            window.location.href = 'faculty_dashboard.php'; // Redirect to dashboard or another page
          </script>";
    exit();
}
// Validate and sanitize input(this code has issue, without this, the delete function works, i think something changed so it doesnt apply anymore.)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid student ID!'); window.location.href='assignments.php?action=read';</script>";
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
            echo "<script>alert('Student deleted successfully!'); window.location.href = 'assignments.php?action=read';</script>";
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
