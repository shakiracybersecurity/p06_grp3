<?php  
// Database connection details
require 'functions.php';
$conn = db_connect();

// Start session
session_start();

checkSessionTimeout();

// Restrict access: Only Admin (role = 3) and Faculty (role = 2) can access
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 3 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit("Unauthorized access.");
}

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
    $submitted_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$submitted_token || $submitted_token !== $_SESSION['csrf_token']) {
        http_response_code(403);
        exit("Invalid CSRF token.");
    }

    // Check if all required inputs exist
    if (isset($_POST['name'], $_POST['phonenumber'], $_POST['email'], $_POST['department_id'], $_POST['id'], $_POST['faculty'])) {
        // Sanitize inputs
        $name = htmlspecialchars(trim($_POST['name']));
        $phonenumber = htmlspecialchars(trim($_POST['phonenumber']));
        $email = htmlspecialchars(trim($_POST['email']));
        $department_id = htmlspecialchars(trim($_POST['department_id']));
        $studentid = htmlspecialchars(trim($_POST['id']));
        $faculty = htmlspecialchars(trim($_POST['faculty']));
        $role_id = 1;  // Default role is 1 for students

        $course_ids = filter_input(INPUT_POST, 'course_ids', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY); // Courses for faculty only

        // Validate phone number
        if (!preg_match('/^\d{8}$/', $phonenumber)) {
            echo "Invalid phone number. It must be exactly 8 digits.<br>";
        } else {
            // Check if the student ID already exists
            $check_stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
            $check_stmt->bind_param("s", $studentid);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                echo "Error: A user with this ID already exists.";
            } else {
                // Register the student
                $register_stmt = $conn->prepare("INSERT INTO students (name, phonenumber, email, department_id, id, faculty, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $register_stmt->bind_param("ssssssi", $name, $phonenumber, $email, $department_id, $studentid, $faculty, $role_id);

                if ($register_stmt->execute()) {
                    $registration_successful = true; // Mark as successful
                    echo "Registration for $name successful!<br>";

                    // Only Faculty can assign courses
                    if ($user_role == 2 && !empty($course_ids)) {
                        $assign_stmt = $conn->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
                        foreach ($course_ids as $course_id) {
                            $assign_stmt->bind_param("ii", $studentid, $course_id);
                            $assign_stmt->execute();
                        }
                        $assign_stmt->close();
                        echo "Courses assigned successfully!<br>";
                    }
                } else {
                    echo "Error: " . $register_stmt->error;
                }

                $register_stmt->close();
            }

            $check_stmt->close();
        }
    } else {
        echo "All fields are required.";
    }
}

// Fetch all courses
$courses = $conn->query("SELECT id, name FROM course");

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
    <select id="department" name="department_id" required>
        <option value="" disabled selected>Select</option>
        <option value="1">RBE/ENG</option>
        <option value="2">RBS/IIT</option>
        <option value="3">RMC/IIT</option>
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

