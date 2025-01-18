<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connection.php'; // Include your DB connection file.

    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $score = $_POST['score'];
    $grade = $_POST['grade'];
    $entered_by = $_SESSION['username']; // Assume a session holds the logged-in user.

    $stmt = $conn->prepare("INSERT INTO grades (STUDENT_ID, COURSE_ID, SCORE, GRADE, ENTERED_BY) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $student_id, $course_id, $score, $grade, $entered_by);

    if ($stmt->execute()) {
        echo "Grade successfully added!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!-- Add Grades Form -->
<form method="POST">
    Student ID: <input type="number" name="student_id" required><br>
    Course ID: <input type="number" name="course_id" required><br>
    Score: <input type="text" name="score" required><br>
    Grade: <input type="text" name="grade" required><br>
    <input type="submit" value="Add Grade">
</form>

