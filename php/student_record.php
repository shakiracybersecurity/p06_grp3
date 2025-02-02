<?php
// Database connection details
require 'functions.php';
$conn = db_connect();
// Start session
session_start();

checkSessionTimeout();
is_logged_in([1]);



$user_role = (int)$_SESSION['role']; // Cast to integer to match type
$user_id = $_SESSION['id'];

//Generate a CSRF token
$csrf_token= bin2hex(random_bytes(32));
$csrf_token_hashed= hash("sha256", $csrf_token);
$issued_at = date("Y-m-d H:i:s");
$expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));



//Store the token in the database
$student_id = ($user_role === 1) ? $user_id : null;

$stmt = $conn->prepare("INSERT INTO csrf (TOKEN, ISSUED_AT, EXPIRES_AT, STUDENT_ID) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $csrf_token_hashed, $issued_at, $expires_at, $student_id);

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

if(!isset($_SESSION['id'])){
    header ('Location:login.php');
    exit;
}

$student_id = $_SESSION['id']; // Ensure this is set correctly from the session

$stmt = $conn->prepare("
    SELECT 
        students.id AS student_id, 
        students.name AS student_name, 
        students.phonenumber, 
        students.email, 
        students.faculty, 
        GROUP_CONCAT(CONCAT(course.name, ' (', student_courses.status, ')') SEPARATOR ', ') AS course_info, 
        IFNULL(GROUP_CONCAT(CONCAT(grades.score, ' (', grades.grade, ')') SEPARATOR ', '), 'No Grade') AS grade_info, 
        department.name AS department_name
    FROM students
    LEFT JOIN student_courses ON students.id = student_courses.student_id
    LEFT JOIN course ON student_courses.course_id = course.id
    LEFT JOIN grades ON students.id = grades.student_id 
                      AND (student_courses.course_id = grades.course_id OR students.grade_id = grades.id) 
    LEFT JOIN department ON students.department_id = department.id
    WHERE students.id=?
    GROUP BY students.id
");

// Bind the student ID to the statement
$stmt->bind_param("i", $student_id); // Use "i" for integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<a href=\"student_dashboard.php\"><button>Back</button></a>";
    echo "<h2>Your Records</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
    <th>ID</th><th>Name</th><th>Phone Number</th><th>Email</th><th>Faculty</th><th>Courses and Status</th><th>Department</th><th>Grades</th>
</tr>";

while ($student = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($student['student_id']) . "</td>";
    echo "<td>" . htmlspecialchars($student['student_name']) . "</td>";
    echo "<td>" . htmlspecialchars($student['phonenumber']) . "</td>";
    echo "<td>" . htmlspecialchars($student['email']) . "</td>";
    echo "<td>" . htmlspecialchars($student['faculty']) . "</td>";
    echo "<td>" . htmlspecialchars($student['course_info']) . "</td>";
    echo "<td>" . htmlspecialchars($student['department_name']) . "</td>";
    echo "<td>" . htmlspecialchars($student['grade_info']) . "</td>";
    echo "</tr>";
}

echo "</table>";
} else {
echo "<p>No student records found.</p>";
}

$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
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
    margin: 0;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color:#b3b4bd; 
    background-size: cover;
    
}
    table {
        border-collapse: collapse;
        width: 80%; /* Adjust width as needed */
        max-width: 1000px; /* Optional: limit table width */
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        margin-left: auto;
        margin-right: auto;
        }
    th, td{
        padding: 15px;
        text-align: center;
        border: 1px solid;
    }
    h2{
    text-align: center;
    color: #2c2e3a;
    margin-top: 30px;
    margin-bottom: 20px;
    }
    th{
        background-color: #0a21c0;
         color: white;
    }
    button {
    background: #fff;
    color: black;
    padding: 10px;
    border: 1px solid #2c2e3a;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 15px;
    border: none;
    }
    button:hover {
    margin-top: 15px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}
    </style>  
<br>
</head>
</html>

