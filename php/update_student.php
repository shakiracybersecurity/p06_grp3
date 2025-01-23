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

$current_student = [];

if (isset($_POST['search_student_id'])){
    $search_student_id = intval($_POST['search_student_id']);
    $stmt = $conn->prepare("SELECT id, name, phonenumber, email, course_id, faculty,department_id,class FROM students WHERE id = ?");
    if (!$stmt){
        die("Query preparation failed: " .$conn->error);
    }

    $stmt->bind_param("i", $search_student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows >0){
        echo "<h1>Update Student Record</h1>";
        echo"<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Name</th><th>Phone Number</th><th>Email</th><th>Faculty</th><th>Class</th>";

    //Display each record
    while ($student = $result->fetch_assoc()){
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
 $stmt->close();
}
?>

<form method="POST">
   <label for= 'search_student_id'>Enter Student ID:</label>
   <input type='text' id='search_student_id' name='search_student_id' required>
   <input type= 'submit' value ='Search'>,<br>
   <a href = "update_details.php">Update student detail</a>
</form>  
