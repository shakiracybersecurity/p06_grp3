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

// Start session
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 1) { // Only Admin can delete
    header("Location: login.php");
    exit("Unauthorized access.");
}

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
    if (!$submitted_token || $submitted_token !== $_SESSION['csrf_token']){
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        exit("Invalid CSRF token.");
    }
}

if(!isset($_SESSION['id'])){
    header ('Location:login.php');
    exit;
}

$student_id = $_SESSION['id'];

 
// Secure: Use prepared statements to prevent SQL injection
 $stmt = $conn->prepare("SELECT 
 students.id AS student_id, 
 students.name AS student_name, 
 students.phonenumber, 
 students.email, 
 students.faculty, 
 students.class, 
 course.name AS course_name, 
 department.name AS department_name
FROM students
LEFT JOIN course ON students.course_id = course.id
LEFT JOIN department ON students.department_id = department.id
WHERE students.id = ?");

 $stmt->bind_param("i",$student_id);
 $stmt->execute();
 $result = $stmt->get_result();


if ($result->num_rows >0){
    echo "<h1>Your Record</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Phone Number</th><th>Email</th><th>Faculty</th><th>Course</th><th>Department</th>";

    //Display each record
    $student = $result->fetch_assoc();
        echo "<tr>";
        echo "<td>" . htmlspecialchars($student['student_id']) . "</td>";
        echo "<td>" . htmlspecialchars($student['student_name']) . "</td>";
        echo "<td>" . htmlspecialchars($student['phonenumber']) . "</td>";
        echo "<td>"  . htmlspecialchars($student['email'])."</td>";
        echo "<td>"  . htmlspecialchars($student['faculty'])."</td>";
        echo "<td>"  . htmlspecialchars($student['course_name']) . "</td>";
        echo "<td>"   .htmlspecialchars($student['department_name']) . "</td>";

    echo "</table>";
 } else{
    echo"<p>No student records found.</p>";
}
//close the statement and connection
$stmt->close();
$conn->close();

?>
<a href="student_dashboard.php">Back</a>
<br>



