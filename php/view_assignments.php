<?php
// Database connection
require 'functions.php';
$conn = db_connect();


session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate token if not already set
}



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
    echo "<a href=\"faculty_dashboard.php\"><button>Back</button></a>";
    echo "<h2>Course Assignments</h2>";
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
        if (!in_array($row['course_status'], ['start', 'in-progress'])) {
            echo "<td>
                <form method='POST' action='delete1.php' onsubmit=\"return confirm('Are you sure you want to delete this assignment?');\">
                <input type='hidden' name='student_id' value='" . htmlspecialchars($row['student_id']) . "'>
                <input type='hidden' name='course_id' value='" . htmlspecialchars($row['course_id']) . "'>
                <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token']) . "'>
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Record</title>
<style>
        *{
    margin: 0;
    box-sizing: border-box;
    font-family: sans-serif;
    }
    body{
    margin-left: auto;
    margin-right: auto;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color:#b3b4bd; 
    background-size: cover;
    }
    table {
        margin-left: auto;
        margin-right: auto;
        border-collapse: collapse;
        width: 80%; /* Adjust width as needed */
        max-width: 1000px; /* Optional: limit table width */
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: 1px solid;
      
        }
        th, td{
        padding: 15px;
        text-align: center;
        border: 1px solid;
        
    }
    th{
        background-color: #0a21c0;
         color: white;
    }
    h2{
    text-align: center;
    color: #2c2e3a;
    margin-top: 30px;
    margin-bottom: 20px;
    }
    button, input[type="submit"] {
        background: #fff;
        color: black;
        padding: 10px;
        border: 1px solid #2c2e3a;
        border-radius: 10px;
        cursor: pointer;
        margin-top: 15px;
        border: none;
    }

    button:hover, input[type="submit"]:hover {
        margin-top: 15px;
        background: #3b3ec0;
        color: white;
        outline: 1px solid #fff;
    }
    </style>
    </head>
</html>