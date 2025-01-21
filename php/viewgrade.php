<?php
// Database connection details
$host = 'localhost';
$dbname = 'robotic course management'; // Ensure this matches your database name
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);
session_start();
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT g.ID, s.NAME AS student_name, c.NAME AS course_name, g.SCORE, g.GRADE 
                        FROM grades g
                        JOIN students s ON g.STUDENT_ID = s.ID
                        JOIN course c ON g.COURSE_ID = c.ID");

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Student</th><th>Course</th><th>Score</th><th>Grade</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['student_name']}</td>
        <td>{$row['course_name']}</td>
        <td>{$row['SCORE']}</td>
        <td>{$row['GRADE']}</td>
    </tr>";
}
echo "</table>";

$conn->close();
?>

<?php if ($_SESSION['role'] == 3){      //redirect back to dashboard of role
    $redirect = "admin_dashboard.php";
}elseif($_SESSION['role'] == 2) 
    $redirect = "faculty_dashboard.php";
?>


<a href="editgrade.php">Edit Student's grade</a>
<br>
<a href="deletegrade.php">Delete Student's grade</a><br>
<a href="<?php echo $redirect; ?>">back</a>