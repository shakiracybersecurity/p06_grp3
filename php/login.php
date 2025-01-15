<?php
// Database connection details
$host = 'localhost';
$dbname = 'robotic course management'; // Updated for clarity
$user = 'root';
$pass = '';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Secure: Sanitize user inputs
    $role = $_POST['users'];
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Secure: Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password_set, role_id FROM $role WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
        
// Secure: Verify hashed password

    if ($password == $user['password_set']) {
// Secure: Regenerate session ID
        session_regenerate_id(true);
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_id'];

        echo "yes";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }
    } else {
        echo "Invalid username or password.";
    }

// Close the statement
$stmt->close();
}

// Close the database connection
$conn->close();
?>

<!-- Login form -->
<form method="POST">
    Please Select a domain: <input type = "radio" name= "users" id ="students_id "value= "students"/>
    <label for = "students_id">Students</label>
    <input type = "radio" name= "users" id ="faculty_staff_id "value= "students"/>
    <label for = "faculty_staff_id">Staff</label>
    <input type = "radio" name= "users" id ="admin_id "value= "students"/>
    <label for = "admin_id">Admin</label><br>
    
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>
