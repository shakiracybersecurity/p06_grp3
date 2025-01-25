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
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 3 && $_SESSION['role'] !=2)) { // Only Admin can delete
    header("Location: login.php");
    exit("Unauthorized access.");
}

$user_role = (int)$_SESSION['role']; // Cast to integer to match type
$user_id = $_SESSION['id'];

//Generate a CSRF token
$csrf_token= bin2hex(random_bytes(32));
$csrf_token_hashed= hash("sha256", $csrf_token);
$issued_at = date("Y-m-d H:i:s");
$expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

//Store the token in the database
$admin_id = ($user_role === 3) ? $user_id : null;
$staff_id = ($user_role === 2) ? $user_id : null;

$stmt = $conn->prepare("INSERT INTO csrf (TOKEN, ISSUED_AT, EXPIRES_AT, ADMIN_ID, STAFF_ID) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssii", $csrf_token_hashed, $issued_at, $expires_at, $admin_id, $staff_id);

if (!$stmt->execute()){
    die("Error inserting CSRF token: " . $stmt->error);
}
$stmt->close();

$_SESSION['csrf_token'] = $csrf_token;

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $submitted_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$submitted_token || $submitted_token !== $_SESSION['csrf_token']){
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        exit("Invalid CSRF token.");
    }
}

// Handle the registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            // ID already exists
            echo "Error: A user with this ID already exists.";
        } else {
            // ID does not exist, proceed with insertion
            $stmt = $conn->prepare("INSERT INTO students (name, phonenumber, email, department_id, id, course_id, faculty, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssi", $name, $phonenumber, $email, $department_id, $studentid, $course_id, $faculty, $role_id);

            if ($stmt->execute()) {
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
    
    <label for="course">Course:</label>
    <select id="course" name = "course_id" required>
        <option value = ""Disabled Select>Select</option>
        <option value = "1">Robotic Engineering</option>
        <option value = "2">Robotic Systems</option>
        <option value = "3">Robotic Mechanics and Control</option>
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
