<?php
require 'functions.php';
$conn = db_connect();

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
        $msg = "Invalid user role selected.";
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

        if (empty($user['password_hash'])){ //first time users redirected to forgot password page
            header("Location: forgot.php");
            exit();
        }
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
            $msg = 'Invalid username or password.</p>';
        }
    } else {
        $msg = 'Invalid username or password.</p>';
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!-- Login form -->
 <!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content="width=device-width, initial-scale=1.0">
        <title> Robotic Management System</title>
 <style>
body{
    margin: 0;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-image: url('background.jpeg');
    background-size: cover;
}
*{
    margin: 0;
    box-sizing: border-box;
    font-family: sans-serif;
}
.container{
    margin-top: 0px;
    margin:50px auto;
    max-width: 500px;
    height: 500px;
    background-color: #fff;
    padding: 30px;
    box-shadow: 0 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    border: 1px solid #fff;
}
h2{
    text-align: center;
    color: #2c2e3a;
    margin-top: 30px;
    margin-bottom: 20px;
}

form{
    display: flex;
    flex-direction: column;
    margin-top: 20px;
}

label{
    font-size: 18px;
    margin-bottom: 5px;
}
input[type="text"],
input[type="password"]{
    padding: 10px;
    margin-top: 25px;
    border: none;
    border-radius: 10px;
    background: transparent;
    border: 1px solid #2c2e3a;
    color: #141619;
    font-size: 13px;
}
.options label {
    margin-top: 15px;
    margin-bottom: 20px;
    font-size: 15px;
    color: #2c2e3a;
}
input[type="radio"]{
    padding: 10px;
    border: none;
    border-radius: 10px;
    background: transparent;
    border: 1px solid #2c2e3a;
    color: #141619;
    font-size: 13px;
    margin-bottom: 20px;
}
.options input{
    margin-right: 5px;
    margin-top: 0px;
}
button {
    background: #fff;
    color: black;
    padding: 10px;
    border: 1px solid #2c2e3a;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 15px;
}
button:hover {
    margin-top: 20px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}
</style>
</head>
 <body>
    <div class="container">
            <?php if (isset($msg)) {echo $msg;}?>
            <h2>Robotic Management System Login</h2>
            <form method="POST">
                Username: <input type="text" name="username" required placeholder="Username"><br>
                Password: <input type="password" name="password" required placeholder="Password"><br>
                
                <div class="options">
                 <p>Please select a domain:</p>
                <input type="radio" name="users" id="students_id" value="students" required>
                <label for="students_id">Students</label>
                <input type="radio" name="users" id="faculty_staff_id" value="faculty" required>
                <label for="faculty_staff_id">Staff</label>
                <input type="radio" name="users" id="admin_id" value="admins" required>
                <label for="admin_id">Admin</label><br> 
    
                <a href="forgot.php">Forgot Password / First Time Login</a><br>
</div>

                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">
                <br><button type="submit" value="Login">Login</button>
            </form>
</div>
</div>
</body>
