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

session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Handle POST request for updating grades
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }
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
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
    <button type="submit">Update Grade</button>
    
</form>
