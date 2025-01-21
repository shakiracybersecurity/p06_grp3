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

// Handle POST request for creating grades
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

<!-- HTML Form for Creating Grades -->
<form method="POST">
    <label for="student_id">Student ID:</label>
    <input type="number" name="student_id" id="student_id" required><br>

    <label for="course_id">Course ID:</label>
    <input type="number" name="course_id" id="course_id" required><br>

    <label for="score">Score:</label>
    <input type="number" step="0.01" name="score" id="score" required><br>

    <label for="grade">Grade:</label>
    <input type="text" name="grade" id="grade" required><br>

    <button type="submit">Create Grade</button>
</form>


