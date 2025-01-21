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
if(isset($_POST['search_student_id'])){
    $search_student_id = intval($_POST['search_student_id']);
    $stmt = $conn-> prepare("SELECT * FROM students WHERE id=?");
    $stmt->bind_param("s" , $search_student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_student = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $phonenumber = htmlspecialchars(trim($_POST['phonenumber']));
    $email = htmlspecialchars(trim($_POST['email']));
    $department_id= htmlspecialchars(trim($_POST['department_id']));
    $studentid = htmlspecialchars(trim($_POST['id']));
    $course_id =htmlspecialchars(trim($_POST['course_id']));
    $faculty = htmlspecialchars(trim($_POST['faculty']));



$stmt = $conn->prepare("UPDATE students SET (name , phonenumber, email, department_id, studentid,course_id, faculty) VALUES (?, ?, ?, ?, ?, ?, ?)WHERE ID = ?");
$stmt->bind_param("ssssssi", $name, $phonenumber, $email, $department_id, $studentid, $course_id, $role_id);

    // Execute query
    if ($stmt->execute()) {
        echo "Student details successfully updated!";
    } else {
        echo "Error updating student details " . $stmt->error;
}

    // Close statement
$stmt->close();
}
?>

<!-- Registration form -->
<form method="POST">
    <label for = "search_student_id">Enter Student ID:</label>
    Student ID : <input type ="text" id= "search_student_id" name="search_student_id" required><br>
    <input type="submit" value="Search">
</form>

<?php if(!empty($current_student)): ?>
<form method="POST">   
    <input type="hidden" name="selected_student_id" value="<?= $current_student['id'] ?>
    Name: <input type="text" id = "name" name="name" required><br>
    Email: <input type="email" id ="email" name="email" required><br>
    Phone Number : <input type ="tel" id = "phonenumber" name = "phonenumber" required><br>
    Student ID : <input type ="text" id= "id" name="id" required><br>

    <label for="department">Department:</label> 
    <select id="department" name="department_id" required>
        <option value = "1">RBE/ENG</option>
        <option value = "2"> RBS/IIT</option>
        <option value = "3"> RMC/IIT</option>
    </select> <br>
    
    <label for="course">Course:</label>
    <select id="course" name = "course_id" required>
        <option value = ""Disabled Select>Select</option>
        <option value = "1">Robotic Engineering</option>
        <option value = "2">Robotic Systems</option>
        <option value = "3">Robotic Mechanics and Control</option>
    </select><br>

    <label for="course">Faculty:</label>
    <select id="faculty" name = "faculty" required>
        <option value = ""Disabled Select>Select</option>
        <option value = "ENG">Engineering</option>
        <option value = "IIT">Informatics and IT </option>
    </select><br>
<input type="submit" name="update_student" value="Update">
</form>

