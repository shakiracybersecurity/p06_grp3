<?php
// Database connection details
$host = 'localhost';
$dbname = 'robotic course management'; 
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request for updating grades
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id']; // Grade ID to update
    $score = $_POST['score']; // New score
    $grade = $_POST['grade']; // New grade

    // Input validation (basic)
    if (empty($id) || empty($score) || empty($grade)) {
        echo "All fields are required.";
    } else {
        // Prepare statement to update grade details
        $stmt = $conn->prepare("UPDATE grades SET SCORE = ?, GRADE = ? WHERE ID = ?");
        $stmt->bind_param("dsi", $score, $grade, $id);

        // Execute query
        if ($stmt->execute()) {
            echo "Grade successfully updated!";
        } else {
            echo "Error updating grade: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!-- Update Grades Form -->
<form method="POST">
    <label for="id">Grade ID:</label>
    <input type="number" name="id" id="id" required><br>

    <label for="score">New Score:</label>
    <input type="text" name="score" id="score" required><br>

    <label for="grade">New Grade:</label>
    <input type="text" name="grade" id="grade" required><br>

    <button type="submit">Update Grade</button>
</form>
