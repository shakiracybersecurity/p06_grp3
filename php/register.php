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

// Handle the registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Check if all required inputs exist
    if (isset($_POST['name'], $_POST['phonenumber'], $_POST['email'], $_POST['department_id'], $_POST['id'], $_POST['course_id'])) {
    // Insecure: No input sanitization or password hashing
    $name = htmlspecialchars(trim($_POST['name']));
    $phonenumber = htmlspecialchars(trim($_POST['phonenumber']));
    $email = htmlspecialchars(trim($_POST['email']));
    $department_id= htmlspecialchars(trim($_POST['department_id']));
    $studentid = htmlspecialchars(trim($_POST['id']));
    $course_id =htmlspecialchars(trim($_POST['course_id']));
    $role_id = 1;  // Default role is 1
}
    // Secure: Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO students (name, phonenumber, email, department_id, id, course_id, role_id) VALUES (?, ?, ?, ?,?, ?, ?)");
    $stmt->bind_param("ssssssi", $name, $phonenumber, $email, $department_id, $studentid, $course_id, $role_id);
    
    if ($stmt->execute()) {
        echo "Registration for $name successful!";
        echo '<br><a href="register.php">Register another student</a>';
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }


    // Close the statement
    $stmt->close();
}
?>

<!-- Registration form -->
<form method="POST">
    Name: <input type="text" id = "name" name="name" required><br>
    Email: <input type="email" id ="email" name="email" required><br>
    Phone Number : <input type ="tel" id = "phonenumber" name = "phonenumber" required><br>
    Student ID : <input type ="text" id= "id" name="id" required><br>

    <label for="department">Department:</label> 
    <select id="department" name="department_id" required>
        <option value ="" disabled select>Select</option>
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

<input type="submit" value="Register">
</form>

