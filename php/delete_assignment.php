<?php


// Ensure the session is started
session_start();
require 'functions.php';

// Check user role for access
if ($_SESSION['role'] != 2) { // Role 3 is Admin
    echo "<script>
        alert('No Access. Only faculty can delete assignments.');
        window.location.href = 'admin_dashboard.php'; // Redirect back
    </script>";
    exit();
}

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    // Debug: Check POST data
    var_dump($_POST);

    $student_id = intval($_POST['student_id']);
    $course_id = intval($_POST['course_id']);
    $csrf_token = $_POST['csrf_token'] ?? '';

    // CSRF Protection
    if ($csrf_token !== $_SESSION['csrf_token']) {
        echo "<script>
            alert('Invalid CSRF token.');
            window.location.href = 'view_assignments.php';
        </script>";
        exit();
    }

    // Fetch course status
    $stmt = $conn->prepare("SELECT status FROM course WHERE id = ?");
    if (!$stmt) {
        echo "<script>
            alert('Database error: Unable to fetch course status.');
            window.location.href = 'view_assignments.php';
        </script>";
        exit();
    }
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    // Allow deletion only if the course is not active
    if (!in_array($status, ['start', 'in-progress'])) {
        $stmt = $conn->prepare("DELETE FROM student_courses WHERE student_id = ? AND course_id = ?");
        if (!$stmt) {
            echo "<script>
                alert('Database error: Unable to delete assignment.');
                window.location.href = 'view_assignments.php';
            </script>";
            exit();
        }
        $stmt->bind_param("ii", $student_id, $course_id);
        if ($stmt->execute()) {
            echo "<script>
                alert('Assignment deleted successfully.');
                window.location.href = 'view_assignments.php';
            </script>";
        } else {
            echo "<script>
                alert('Error deleting assignment.');
                window.location.href = 'view_assignments.php';
            </script>";
        }
        $stmt->close();
    } else {
        echo "<script>
            alert('Cannot delete assignment. The course is currently active.');
            window.location.href = 'view_assignments.php';
        </script>";
    }
    exit();
}

$conn->close();
?>
