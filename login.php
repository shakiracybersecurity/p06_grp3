<?php
// Database connection details
$host = 'localhost';
$dbname = 'insecure_login_system'; // Updated for clarity
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
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Secure: Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

// Check if the user exists
if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
        
// Secure: Verify hashed password
    if (password_verify($password, $user['password'])) {
// Secure: Regenerate session ID
        session_regenerate_id(true);
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

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
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>
