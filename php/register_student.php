<?php  
// Database connection details
require 'functions.php';
$conn = db_connect();

// Start session
session_start();
is_logged_in([3,2]);
checkSessionTimeout();


$user_id = $_SESSION['id'];
$user_role = (int)$_SESSION['role']; // Cast role to integer

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Track registration success
$registration_successful = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    $submitted_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
    if (!$submitted_token || $submitted_token !== $_SESSION['csrf_token']) {
        http_response_code(403);
        exit("Invalid CSRF token.");
    }

    // Use the `registerStudent` function to handle registration
    $message = registerStudent($_POST, $_SESSION['role']);

    // Check if registration was successful
    if (strpos($message, "successful") !== false) {
        $registration_successful = true;
        echo "<script>
            alert('$message');
            window.location.href = 'assignments.php?action=read';
        </script>";
    } else {
        echo "<script>
            alert('$message');
        </script>";
    }
}

// Fetch all courses
$courses = $conn->query("SELECT id, name FROM course");
$departments = getDepartments();
?>
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
        background-color:#2c2e3a;
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
        height: 900px;
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 0px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        border: 1px solid #fff;
    }
    h1{
        text-align: center;
        color: #050A44;
        margin-top: 30px;
        margin-bottom: 20px;
    }
    form{
        display: flex;
        flex-direction: column;
        margin-top: 20px;
    }
    label{
        font-size: 15px;
        margin-bottom: 2px;
    }
    input[type="text"],
    input[type="email"], input[type="tel"]{
        padding: 10px;
        margin-top: 8px;
        border: none;
        border-radius: 15px;
        background: transparent;
        border: 1px solid #2c2e3a;
        color: #141619;
        font-size: 15px;
    }
    .options label {
        margin-top: 20px;
        margin-bottom: 30px;
        font-size: 15px;
        color: #2c2e3a;
    }
    input[type="checkbox"]{
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
        margin-top: 10px;
    }
    select{
        width: 300px; /* Adjust width */
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        color: #333;
        margin-bottom: 20px;
    }
    select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    button {
        background: #fff;
        color: black;
        padding: 10px;
        border: 1px solid #2c2e3a;
        border-radius: 10px;
        cursor: pointer;
        margin-top: 15px;
        display:flex;
    }
    button:hover {
        margin-top: 15px;
        background: #3b3ec0;
        color: white;
        outline: 1px solid #fff;
    }
    a {
        text-decoration: none;
    }
    </style>
</head>
<body>
<a href="<?= $user_role == 2 ? 'faculty_dashboard.php' : 'admin_dashboard.php' ?>"><button>Back</button></a>
<!-- Display the form only if registration was not successful -->
<?php if (!$registration_successful): ?>
    <div class="container">

    <form method="POST">
    <h1>Register New Student</h1>
    Name: <input type="text" id="name" name="name" required placeholder="Name"><br>
    Email: <input type="email" id="email" name="email" required placeholder="Email"><br>
    Phone Number: <input type="tel" id="phonenumber" name="phonenumber" required placeholder="Phone Number" pattern="\d{8}" title="Phone number must be 8 digits"><br>
    Student ID: <input type="text" id="id" name="id" required placeholder="Student"><br>
    
    <div class = "dropdown">
    <label for="department">Department:</label> 
    <select name="department_id" id="department" required>
        <option value="" disabled selected>Select Department</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
        <?php endforeach; ?>
    </select><br>
    

    <label for="faculty">Faculty:</label>
    <select id="faculty" name="faculty" required>
        <option value="" disabled selected>Select</option>
        <option value="ENG">Engineering</option>
        <option value="IIT">Informatics and IT</option>
    </select><br>
</div>
<div class = "options">
        <label for="course">Courses:</label><br>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <input type="checkbox" name="course_ids[]" value="<?= $course['id'] ?>"> <?= htmlspecialchars($course['name']) ?><br>
        <?php endwhile; ?>
    <?php endif; ?>

    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <button type="submit" value="Register">Register<br>
        </div>
</div>
</body>
</form>
</body>
</form>
</html>

