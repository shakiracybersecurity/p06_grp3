<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database functions
require 'functions.php';
$conn = db_connect();

// Ensure database connection is established
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Start session and check session variables
session_start();
checkSessionTimeout();

// Allow only Admin (role_id = 3) or Faculty (role_id = 2) to access
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], [2, 3])) {
    header("Location: unauthorized.php");
    exit();
}

// Generate and store a new CSRF token if it doesn't exist
if (empty($_SESSION['csrf_plain'])) {
    $_SESSION['csrf_plain'] = bin2hex(random_bytes(32)); // Store plain token
    $_SESSION['csrf_hash'] = password_hash($_SESSION['csrf_plain'], PASSWORD_DEFAULT); // Store hashed token
}

// Fetch courses for the dropdown
$course_result = $conn->query("SELECT ID, NAME FROM course");
if (!$course_result) {
    die("Error fetching courses: " . $conn->error);
}
$courses = $course_result->fetch_all(MYSQLI_ASSOC);

// Search for students by name (optional)
$search_name = $_GET['search_name'] ?? '';
$students = [];
if (!empty($search_name)) {
    $stmt = $conn->prepare("SELECT ID, NAME FROM students WHERE NAME LIKE ?");
    $like_name = "%" . $search_name . "%";
    $stmt->bind_param("s", $like_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle grade creation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = htmlspecialchars($_POST['token'] ?? '', ENT_QUOTES, 'UTF-8');

    // Validate CSRF token
    if (!$token || !password_verify($token, $_SESSION['csrf_hash'])) {
        die("Error: CSRF token verification failed.");
    }

    // Regenerate new CSRF token for next request
    unset($_SESSION['csrf_plain'], $_SESSION['csrf_hash']);
    $_SESSION['csrf_plain'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_hash'] = password_hash($_SESSION['csrf_plain'], PASSWORD_DEFAULT);

    $student_id = $_POST['student_id']; // ID of the student
    $course_id = $_POST['course_id']; // ID of the course
    $score = $_POST['score']; // Score
    $grade = $_POST['grade']; // Grade

    // Input validation
    if (empty($student_id) || empty($course_id) || empty($score) || empty($grade)) {
        $error_message = "All fields are required.";
    } else {
        // Check if student ID exists before proceeding
        $student_check_stmt = $conn->prepare("SELECT ID FROM students WHERE ID = ?");
        $student_check_stmt->bind_param("i", $student_id);
        $student_check_stmt->execute();
        $student_check_stmt->store_result();

        if ($student_check_stmt->num_rows === 0) { 
            $error_message = "Unable to create grade: Invalid Student ID entered"; // Set error message
            $student_check_stmt->close();
        } else {
            // Proceed only if the student ID is valid
            $student_check_stmt->close(); // Close here instead of inside the if-block

            // Check if a grade record already exists for this student and course
            $check_stmt = $conn->prepare("SELECT ID FROM grades WHERE STUDENT_ID = ? AND COURSE_ID = ?");
            $check_stmt->bind_param("ii", $student_id, $course_id);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $error_message = "A grade record already exists for this student under the selected course.";
            } else {
                // Prepare the SQL statement to insert data securely
                $stmt = $conn->prepare("INSERT INTO grades (STUDENT_ID, COURSE_ID, SCORE, GRADE) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iids", $student_id, $course_id, $score, $grade);

                if ($stmt->execute()) {
                    $success_message = "Grade successfully added!";
                } else {
                    $error_message = "Error adding grade: " . $stmt->error;
                }

                $stmt->close();
            }
            $check_stmt->close();
        }
        
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content="width=device-width, initial-scale=1.0">
        <title> Robotic Management System</title>
<!-- Style for Form and Search Results -->
<style>
    * {
        margin: 0;
        box-sizing: border-box;
        font-family: sans-serif;
    }
    body {
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #2c2e3a;
        background-size: cover;
    }
    .container {
        margin: 50px auto;
        max-width: 500px;
        height: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
    }
    form {
        width: 50%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 10px;
        background-color: #f9f9f9;
    }
    h1 {
        text-align: center;
        color: #050A44;
        margin-bottom: 20px;
    }
    input, select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
    }
    .search-results {
        width: 50%;
        margin: 10px auto;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }
    button {
        background: #fff;
        color: black;
        padding: 10px;
        border-radius: 10px;
        cursor: pointer;
        margin-top: 15px;
    }
    button:hover {
        background: #3b3ec0;
        color: white;
    }
</style>

<br>
<a href="viewgradetry.php"><button>Back to View Student Grades</button></a>

<form method="GET">
    <h1>Create Grade</h1>
    <label for="search_name">Search Student by Name:</label>
    <input type="text" name="search_name" id="search_name" placeholder="Enter student name">
    <button type="submit">Search</button>
</form>

<?php if (!empty($students)): ?>
    <div class="search-results">
        <h4>Search Results:</h4>
        <?php foreach ($students as $student): ?>
            <div>
                <span><strong>Name:</strong> <?php echo htmlspecialchars($student['NAME']); ?></span>
                <span><strong>ID:</strong> <?php echo htmlspecialchars($student['ID']); ?></span>
                <button onclick="selectStudent('<?php echo $student['ID']; ?>')">Select</button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST">
    <label for="student_id">Student ID:</label>
    <input type="number" name="student_id" id="student_id" required><br>

    <label for="course">Course:</label>
    <select id="course" name="course_id" required>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['ID']; ?>">
                <?php echo htmlspecialchars($course['NAME']); ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="score">Score:</label>
    <input type="number" step="0.1" name="score" id="score" required><br>

    <label for="grade">Grade:</label>
    <select name="grade" id="grade" required>
        <option value="A">A</option>
        <option value="B+">B+</option>
        <option value="B">B</option>
        <option value="C+">C+</option>
        <option value="C">C</option>
        <option value="D+">D+</option>
        <option value="D">D</option>
        <option value="F">F</option>
    </select><br>

    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_plain'] ?? '') ?>">
    <button type="submit">Create Grade</button>
</form>

<script>
    function selectStudent(studentId) {
        document.getElementById('student_id').value = studentId;
    }
</script>

<!-- Confirmation Message -->
<?php if (!empty($success_message)): ?>
    <p style="color: white;">
        <?php echo $success_message; ?> 
        <a href="viewgradetry.php" style="color: darkcyan;">Return back to student list?</a>
    </p>
<?php elseif (!empty($error_message)): ?>
    <p style="color: #B22222 ;"><?php echo $error_message; ?></p>
<?php endif; ?>
