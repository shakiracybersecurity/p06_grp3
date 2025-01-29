<?php
// Database connection details
require 'functions.php';
$conn = db_connect();

session_start();

checkSessionTimeout();

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Allow only Admin (role_id = 3) or Faculty (role_id = 2) to access
if (!in_array($_SESSION['role'], [2, 3])) {
    header("Location: unauthorized.php");
    exit();
}

// Fetch courses for the dropdown
$course_result = $conn->query("SELECT ID, NAME FROM course");
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
        $error_message = "All fields are required.";
    } else {
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
            $stmt->bind_param("iids", $student_id, $course_id, $score, $grade); // i = integer, d = double, s = string

            // Execute the statement
            if ($stmt->execute()) {
                $success_message = "Grade successfully added!";
            } else {
                $error_message = "Error adding grade: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        }
        $check_stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!-- Style for Form and Search Results -->
<style>
    form {
        width: 50%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 10px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    h3 {
        text-align: center;
        color: #4CAF50;
        font-size: 24px;
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }

    input[type="number"],
    input[type="text"],
    select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .search-results {
        width: 40%;
        margin: 10px auto;
        padding: 15px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
    }

    .search-results h4 {
        margin-bottom: 10px;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
    }

    .search-results .student-details {
        margin-bottom: 15px;
        padding: 10px;
        border-bottom: 1px solid #ddd;
        display: flex;
        flex-direction: column;
    }

    .student-details span {
        margin-bottom: 5px;
    }

    .search-results button {
        align-self: center;
        padding: 10px 20px;
        font-size: 14px;
        color: white;
        background-color: #4CAF50;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .search-results button:hover {
        background-color: #45a049;
    }

    button {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
    }

    button:hover {
        background-color: #45a049;
    }

    p {
        text-align: center;
        font-size: 16px;
        margin-top: 20px;
    }

    p a {
        color: #4CAF50;
        text-decoration: none;
        font-weight: bold;
    }

    p a:hover {
        text-decoration: underline;
    }
</style>

<br>
<a href="viewgradetry.php">Back to View Student Grades</a>

<h3>Create Grade</h3>
<form method="GET">
    <label for="search_name">Search Student by Name:</label>
    <input type="text" name="search_name" id="search_name" placeholder="Enter student name">
    <button type="submit">Search</button>
</form>

<?php if (!empty($students)): ?>
    <div class="search-results">
        <h4>Search Results:</h4>
        <?php foreach ($students as $student): ?>
            <div class="student-details">
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

    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
    <button type="submit">Create Grade</button>
</form>

<!-- Confirmation Message -->
<?php if (!empty($success_message)): ?>
    <p><?php echo $success_message; ?> <a href="viewgradetry.php">Return back to student list?</a></p>
<?php elseif (!empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<script>
    function selectStudent(studentId) {
        document.getElementById('student_id').value = studentId;
    }
</script>
