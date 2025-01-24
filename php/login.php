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
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();}
// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }
    
    if (!isset($_POST['users']) || !in_array($_POST['users'],['students', 'faculty', 'admins'])){
        echo "Please select your role before logging in.";
    } else {
        $role = $_POST['users'];
    
    
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
    if (password_verify($password, $user['password_hash'])){
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
}
// Close the database connection
$conn->close();
?>

<!-- Login form -->
<form method="POST">
    
    Please Select a domain: <input type = "radio" name= "users" id ="students_id "value= "students"/>
    <label for = "students_id">Students</label>
    <input type = "radio" name= "users" id ="faculty_staff_id "value= "faculty"/>
    <label for = "faculty_staff_id">Staff</label>
    <input type = "radio" name= "users" id ="admin_id "value= "admins"/>
    <label for = "admin_id">Admin</label><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <a href="forgot.php">Forgot Password / First Time Login</a>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
    <input type="submit" value="Login">

    
</form>
