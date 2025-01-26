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

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 3 && $_SESSION['role'] != 2)) { // Only Admin or Faculty can access
    header("Location: login.php");
    exit("Unauthorized access.");
}

$user_role = (int)$_SESSION['role']; // Cast to integer to match type
$user_id = $_SESSION['id'];

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    $submitted_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$submitted_token || hash_equals($_SESSION['csrf_token'], $submitted_token) === false) {
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
        $course_ids = filter_input(INPUT_POST, 'course_ids', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        $role_id = 1;  // Default role is 1

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
                echo "Registration for $name successful!";
                echo '<br><a href="register.php">Register another student</a>';
                echo '<br><a href="admin_dashboard.php">Back</a>';

                // Assign courses if selected
                if (!empty($course_ids)) {
                    $assign_stmt = $conn->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
                    foreach ($course_ids as $course_id) {
                        $assign_stmt->bind_param("ii", $studentid, $course_id);
                        $assign_stmt->execute();
                    }
                    $assign_stmt->close();
                    echo "<br>Courses are assigned successfully!";
                }
            } else {
                echo "Error: " . $register_stmt->error;
            }

            $register_stmt->close();
        }

        $check_stmt->close();
    } else {
        echo "All fields are required.";
    }
}



$students = $conn->query("SELECT * FROM students");
$courses = $conn->query("SELECT * FROM course");

?>

<!-- Registration form -->
<form method="POST">
    <h1> Register new student</h1>
    Name: <input type="text" id = "name" name="name" required><br>
    Email: <input type="email" id ="email" name="email" required><br>
    Phone Number : <input type ="tel" id = "phonenumber" name = "phonenumber" required><br>
    Student ID : <input type ="text" id= "id" name="id" required><br>

    <label for="department">Department:</label> 
    <select id="department" name="department_id" required>
        <option value ="" disabled select>Select</option>
        <option value = "1">RBE/ENG</option>
        <option value = "2"> RBS/IIT</option>
        <option value = "3"> RMC/IIT</option>
    </select> <br>
    
    <label for="course">Assign Courses:</label><br>
        <input type ="checkbox" name = "course_ids[]" value="1" id="course_1">Robotic Engineering<br>
        <input type ="checkbox" name = "course_ids[]" value="2" id="course_2">Robotic Systems<br>
        <input type ="checkbox" name = "course_ids[]" value="3" id="course_3">Robotic Mechanics and Control<br>
    </select><br>

    <label for="faculty">Faculty:</label>
    <select id="faculty" name = "faculty" required>
        <option value = ""Disabled Select>Select</option>
        <option value = "ENG">Engineering</option>
        <option value = "IIT">Informatics and IT </option>
    </select><br>
<input type="submit" value="Register"><br>



<input type = "hidden" name ="token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']);?>">

</form>
<a href="admin_dashboard.php" style="display: inline-block; padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Back</a>
