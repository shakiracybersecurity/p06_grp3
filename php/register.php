<?php
// Database connection details
$host = 'localhost';
$dbname = 'insecure_login_system';
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
    // Insecure: No input sanitization or password hashing
    $name = ($_POST['name']);
    $phonenumber = ($_POST['phonenumber']);
    $email = ($_POST['email']);
    $department = ($_POST['department']);
    $studentid = ($_POST['id']);
    $course_id =($_POST['course_id']);
    $role_id = ($_POST['role_id']);  // Default role is 'user'

    // Secure: Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);
    
    if ($stmt->execute()) {
        echo "Registration successful!";
        header("Location: login.php");
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
    <body>
    Department : 
    <select id="department" name="department_id" required>
        <option value ="" Disabled Select>Select</option>
        <option value = "1">RBE/ENG</option>
        <option value = "2"> RBS/IIT</option>
        <option value = "3"> RMC/IIT</option>
    </select> <br>
    Course:
    <select id="course" name = "course_id" required>
        <option value = ""Disabled Select>Select</option>
        <option value = "1">Robotic Engineering</option>
        <option value = "2">Robotic Systems</option>
        <option value = "3">Robotic Mechanics and Control</option>
    </select> 
</body>   <br>
<input type="submit" value="Register">
</form>

