<?php
// Database connection details
$host = 'localhost';
$dbname = 'robotic course management';
$user = 'root';  // Replace with your MySQL username
$pass = '';      // Replace with your MySQL password

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Handle the registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Secure: Sanitize and validate input
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Check if username and password are valid
    if (empty($username) || empty($password)) {
        echo "Please provide a valid username and password.";
        exit();
    }
    
    // Secure: Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user'; // Default role is 'user'

    // Secure: Use prepared statements
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $username, $hashedPassword, $role);
        
        if ($stmt->execute()) {
            echo "Registration successful!";
            header("Location: login.php");
            exit();
        } else {
            echo "Error during registration.";
        }
        $stmt->close();
    } else {
        echo "Failed to prepare the statement.";
    }
}

// Close the database connection
$conn->close();
?>

<!-- Registration form -->
<form method="POST">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Register">
</form>

