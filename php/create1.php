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
require "functions.php";

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

<!-- Display the form only if registration was not successful -->
<?php if (!$registration_successful): ?>
<form method="POST">
    <h1>Register New Student</h1>
    Name: <input type="text" id="name" name="name" required><br>
    Email: <input type="email" id="email" name="email" required><br>
    Phone Number: <input type="tel" id="phonenumber" name="phonenumber" required pattern="\d{8}" title="Phone number must be 8 digits"><br>
    Student ID: <input type="text" id="id" name="id" required><br>

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

    <?php if ($user_role == 2): // Show course assignment only for faculty ?>
        <label for="course">Assign Courses:</label><br>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <input type="checkbox" name="course_ids[]" value="<?= $course['id'] ?>"> <?= htmlspecialchars($course['name']) ?><br>
        <?php endwhile; ?>
    <?php endif; ?>

    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="submit" value="Register"><br>
</form>
<?php endif; ?>

<a href="<?= $user_role == 2 ? 'faculty_dashboard.php' : 'admin_dashboard.php' ?>" 
   style="display: inline-block; padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">
    Back
</a>
