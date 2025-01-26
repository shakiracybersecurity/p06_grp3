<?php
// Database connection details
$host = 'localhost';
$dbname = 'robotic course management';
$user = 'root';
$pass = '';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();
$_SESSION['last_activity'] = time();

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$token || $token !== $_SESSION['token']) {
        // Return 405 HTTP status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }

    // Validate `users` input
    $valid_roles = ['students', 'faculty', 'admins'];
    $role = $_POST['users'] ?? ''; // Default to empty string if not set

    if (!in_array($role, $valid_roles)) {
        echo "Invalid user role selected.";
        exit;
    }

    // Secure: Sanitize user inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Secure: Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password_hash, role_id FROM $role WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Secure: Verify hashed password stored in password_hash column
        if (password_verify($password, $user['password_hash'])) {
            // Secure: Regenerate session ID
            session_regenerate_id(true);
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = $user['role_id'];

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
    <p>Please select a domain:</p>
    <input type="radio" name="users" id="students_id" value="students" required>
    <label for="students_id">Students</label>
    <input type="radio" name="users" id="faculty_staff_id" value="faculty" required>
    <label for="faculty_staff_id">Staff</label>
    <input type="radio" name="users" id="admin_id" value="admins" required>
    <label for="admin_id">Admin</label><br> 

    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <a href="forgot.php">Forgot Password / First Time Login</a>
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">
    <input type="submit" value="Login">
</form>
