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

// Handle POST request for deleting grades
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id']; // Grade ID to delete

    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }

    // Input validation (basic)
    if (empty($id)) {
        echo "Grade ID is required.";
    } else {
        // Check if the associated course has ended
        $stmt_check = $conn->prepare("SELECT c.END_DATE 
                                      FROM grades g
                                      JOIN course c ON g.COURSE_ID = c.ID
                                      WHERE g.ID = ?");
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $course = $result->fetch_assoc();
        $stmt_check->close();

        if ($course && strtotime($course['END_DATE']) < time()) {
            // Course has ended, proceed to delete the grade
            $stmt = $conn->prepare("DELETE FROM grades WHERE ID = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "Grade successfully deleted!";
            } else {
                echo "Error deleting grade: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Cannot delete grade because the course is still active.";
        }
    }
}

// Close the database connection
$conn->close();
?>

<!-- Delete Grades Form -->
<form method="POST">
    <label for="id">Grade ID:</label>
    <input type="number" name="id" id="id" required><br>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
    <button type="submit">Delete Grade</button>
</form>
