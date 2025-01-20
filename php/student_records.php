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
 $stmt = $conn->prepare("SELECT id, name, phonenumber, email, course_id, faculty,department_id,class FROM students");
 $stmt->execute();
 $result = $stmt->get_result();


if ($result->num_rows >0){
    echo "<h1>Current Student Records</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Phone Number</th><th>Email</th><th>Faculty</th><th>Class</th>";

    //Display each record
    while ( $student = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>" . htmlspecialchars($student['id']) . "</td>";
        echo "<td>" . htmlspecialchars($student['name']) . "</td>";
        echo "<td>" . htmlspecialchars($student['phonenumber']) . "</td>";
        echo "<td>"  . htmlspecialchars($student['email'])."</td>";
        echo "<td>"  . htmlspecialchars($student['faculty'])."</td>";
        echo "<td>"  . htmlspecialchars($student['class'])."</td>";
    }   

    echo "</table>";
 } else{
    echo"<p>No student records found.</p>";
}

//close the statement and connection
$stmt->close();
$conn->close();
?>
<a href="admin_dashboard.php">Back</a>
<br>
<a href="register.php">Register a new student</a>


