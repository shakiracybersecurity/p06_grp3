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
LEFT JOIN department ON students.department_id = department.id");
 $stmt->execute();
 $result = $stmt->get_result();


if ($result->num_rows >0){
    echo "<h1>Current Student Records</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Phone Number</th><th>Email</th><th>Faculty</th><th>Course</th><th>Department</th>";

    //Display each record
    while ( $student = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>" . htmlspecialchars($student['student_id']) . "</td>";
        echo "<td>" . htmlspecialchars($student['student_name']) . "</td>";
        echo "<td>" . htmlspecialchars($student['phonenumber']) . "</td>";
        echo "<td>"  . htmlspecialchars($student['email'])."</td>";
        echo "<td>"  . htmlspecialchars($student['faculty'])."</td>";
        echo "<td>"  . htmlspecialchars($student['course_name'])."</td>";
        echo "<td>"  . htmlspecialchars($student['department_name'])."</td>";
        echo "<td><a href = 'update_student.php?id=" . $student['student_id'] . "'>Update</a></td>";
        echo "<td>
            <form method = 'POST' action='delete_student.php' onsubmit='return confirm(\"Are you sure you want to delete this record?\");'>
            <input type='hidden' name='id' value='" . $student['student_id'] . "'>
            <input type = 'submit' name='delete' value='Delete'>
            </form>
            </td>";
        echo "</tr>";
    }   

    echo "</table>";
 } else{
    echo"<p>No student records found.</p>";
}

//close the statement and connection
$stmt->close();
$conn->close();

if ($_SESSION['role'] == 3){      //redirect back to dashboard of role
    $redirect = "admin_dashboard.php";
}elseif($_SESSION['role'] == 2) 
    $redirect = "faculty_dashboard.php";
?>
<a href="<?php echo $redirect; ?>">Back</a>
<br>



