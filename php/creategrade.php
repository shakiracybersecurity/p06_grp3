<?php
// Database connection details
$host = 'localhost';
$dbname = 'robotic course management'; // Ensure this matches your database name
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Fetch courses for the dropdown
$course_result = $conn->query("SELECT ID, NAME FROM course");
$courses = $course_result->fetch_all(MYSQLI_ASSOC);

// Handle POST request for creating grades
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }
    $student_id = $_POST['student_id']; // ID of the student
    $course_id = $_POST['course_id']; // ID of the course
    $score = $_POST['score']; // Score
    $grade = $_POST['grade']; // Grade

    // Input validation
    if (empty($student_id) || empty($course_id) || empty($score) || empty($grade)) {
        echo "All fields are required.";
    } else {
        // Prepare the SQL statement to insert data securely
        $stmt = $conn->prepare("INSERT INTO grades (STUDENT_ID, COURSE_ID, SCORE, GRADE) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $student_id, $course_id, $score, $grade); // i = integer, d = double, s = string

        // Execute the statement
        if ($stmt->execute()) {
            echo "Grade successfully added!";
        } else {
            echo "Error adding grade: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!--form for creating grade-->
<!-- HTML Form for Creating Grades -->
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

    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
    <button type="submit">Create Grade</button>
</form>

