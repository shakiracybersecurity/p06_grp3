<?php
// Ensure the session is started
session_start();
require 'functions.php';

$conn = db_connect();

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
    if (!in_array($status, ['start', 'in-progress'])) {
        $stmt = $conn->prepare("DELETE FROM student_courses WHERE student_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $student_id, $course_id);
        $stmt->execute();
        $stmt->close();
         // Success message as a JavaScript alert
        echo "<script>
        alert('Assignment deleted successfully.');
        window.location.href = 'view_assignments.php'; // Redirect after showing the alert
        </script>";
    } else {
    // Failure message as a JavaScript alert
        echo "<script>
        alert('Cannot delete assignment. The course is currently active.');
        window.location.href = 'view_assignments.php'; // Redirect back
        </script>";
}
}
$conn->close();
?>
