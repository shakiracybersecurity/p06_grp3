<?php
// Database connection
require 'functions.php';
$conn = db_connect();

// Start session and check role
session_start();

checkSessionTimeout();

// Only Faculty can delete course assignments
if (!isset($_SESSION['username']) || $_SESSION['role'] != 2) {
    header("Location: login.php");
    exit();
}

// Corrected Query
$query = "
SELECT
    student_courses.student_id,
    student_courses.course_id,
    course.name AS course_name,
    course.status AS course_status,
    students.name AS student_name
FROM student_courses
INNER JOIN students ON student_courses.student_id = students.id
INNER JOIN course ON student_courses.course_id = course.id
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<h1>Course Assignments</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
        <th>Student Name</th>
        <th>Course Name</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['course_status']) . "</td>";
        

        // Check if the course can be deleted
        if (!in_array($row['course_status'], ['start', 'in-progress', 'ended'])) {
            echo "<td>
                <form method='POST' action='delete1.php' onsubmit=\"return confirm('Are you sure you want to delete this assignment?');\">
                    <input type='hidden' name='student_id' value='" . htmlspecialchars($row['student_id']) . "'>
                    <input type='hidden' name='course_id' value='" . htmlspecialchars($row['course_id']) . "'>
                    <input type='submit' name='delete' value='Delete'>
                </form>
            </td>";
        } else {
            echo "<td>Cannot Delete (Active Course)</td>";
        }

        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No course assignments found.</p>";
}

$conn->close();
?>
<a href = "faculty_dashboard.php">Back</a>