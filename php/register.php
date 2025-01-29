<?php 
// Database connection details
require 'functions.php';
$conn = db_connect();

// Start session
session_start();

checkSessionTimeout();

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 3 && $_SESSION['role'] != 2)) { // Only Admin or Faculty can register
    header("Location: login.php");
    exit("Unauthorized access.");
}

$user_role = (int)$_SESSION['role']; // Cast to integer to match type
$user_id = $_SESSION['id'];

// Generate a CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
}

// Initialize registration status
$registration_successful = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    $submitted_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$submitted_token || $submitted_token !== $_SESSION['csrf_token']) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        exit("Invalid CSRF token.");
    }

    // Check if all required inputs exist
    if (isset($_POST['name'], $_POST['phonenumber'], $_POST['email'], $_POST['department_id'], $_POST['id'], $_POST['course_id'], $_POST['faculty'])) {
        // Sanitize inputs
        $name = htmlspecialchars(trim($_POST['name']));
        $phonenumber = htmlspecialchars(trim($_POST['phonenumber']));
        $email = htmlspecialchars(trim($_POST['email']));
        $department_id = htmlspecialchars(trim($_POST['department_id']));
        $studentid = htmlspecialchars(trim($_POST['id']));
        $course_id = htmlspecialchars(trim($_POST['course_id']));
        $faculty = htmlspecialchars(trim($_POST['faculty']));
        $role_id = 1;  // Default role is 1

        // Check if the ID already exists
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
        $check_stmt->bind_param("s", $studentid);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "Error: A user with this ID already exists.";
        } else {
            // ID does not exist, proceed with insertion
            $stmt = $conn->prepare("INSERT INTO students (name, phonenumber, email, department_id, id, course_id, faculty, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssi", $name, $phonenumber, $email, $department_id, $studentid, $course_id, $faculty, $role_id);

            if ($stmt->execute()) {
                $registration_successful = true; // Set success flag
                echo "Registration for $name successful!";
                echo '<br><a href="register.php">Register another student</a>';
                echo '<br><a href="admin_dashboard.php">Back</a>';
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $check_stmt->close();
    } else {
        echo "All fields are required.";
    }
}
?>

<?php if (!$registration_successful): ?>
<!-- Registration form -->
<form method="POST">
    <h1> Register new student</h1>
    Name: <input type="text" id="name" name="name" required><br>
    Email: <input type="email" id="email" name="email" required><br>
    Phone Number: <input type="tel" id="phonenumber" name="phonenumber" required><br>
    Student ID: <input type="text" id="id" name="id" required><br>

    <label for="department">Department:</label> 
    <select id="department" name="department_id" required>
        <option value="" disabled selected>Select</option>
        <option value="1">RBE/ENG</option>
        <option value="2">RBS/IIT</option>
        <option value="3">RMC/IIT</option>
    </select> <br>
    
    <label for="course">Course:</label>
    <select id="course" name="course_id" required>
        <option value="" disabled selected>Select</option>
        <option value="1">Robotic Engineering</option>
        <option value="2">Robotic Systems</option>
        <option value="3">Robotic Mechanics and Control</option>
    </select><br>

    <label for="faculty">Faculty:</label>
    <select id="faculty" name="faculty" required>
        <option value="" disabled selected>Select</option>
        <option value="ENG">Engineering</option>
        <option value="IIT">Informatics and IT</option>
    </select><br>

    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <input type="submit" value="Register"><br>
</form>
<?php endif; ?>
