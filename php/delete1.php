<?php
// Database connection
require 'functions.php';
$conn = db_connect();

// Start session and check role
session_start();

checkSessionTimeout();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 2) { // Only Faculty can delete assignments
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $student_id = intval($_POST['student_id']);
    $course_id = intval($_POST['course_id']);

    // Fetch course status
    $stmt = $conn->prepare("SELECT status FROM course WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    // Allow deletion only if the course is not active
    if (!in_array($status, ['start', 'in-progress', 'ended'])) {
        $stmt = $conn->prepare("DELETE FROM student_courses WHERE student_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $student_id, $course_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Course assignment deleted successfully.";
        } else {
            $_SESSION['message'] = "Error deleting course assignment: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Cannot delete course assignment. The course is currently active.";
    }

    header("Location: view_assignments.php");
    exit();
}

$conn->close();
?>
