<?php
// Database connection
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
require "functions.php";

checkSessionTimeout();

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Allow only Admin (role_id = 3) or Faculty (role_id = 2) to access
if (!in_array($_SESSION['role'], [2, 3])) {
    header("Location: unauthorized.php");
    exit();
}

// Get the grade ID from the URL
$grade_id = $_GET['id'] ?? '';
if (empty($grade_id)) {
    die("Grade ID is required.");
}

// Fetch student and grade details
$stmt = $conn->prepare("SELECT g.ID, s.NAME AS student_name
                        FROM grades g
                        LEFT JOIN students s ON g.STUDENT_ID = s.ID
                        WHERE g.ID = ?");
$stmt->bind_param("i", $grade_id);
$stmt->execute();
$result = $stmt->get_result();
$grade = $result->fetch_assoc();
$stmt->close();

if (!$grade) {
    die("Grade not found.");
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
        $stmt = $conn->prepare("DELETE FROM grades WHERE ID = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $success_message = "Grade successfully deleted!";
        } else {
            $error_message = "Error deleting grade: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!-- Delete Grades Form -->
<style>
    form {
        width: 50%;
        margin: 30px auto;
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

    input[type="number"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
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

<form method="POST">
    <h3>You are deleting <?php echo htmlspecialchars($grade['student_name']); ?>'s grade</h3>
    
    <label for="id">Grade ID:</label>
    <input type="number" name="id" id="id" value="<?php echo htmlspecialchars($grade['ID']); ?>" readonly><br>
    
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
    <button type="submit">Delete Grade</button>
</form>

<!-- Confirmation Message -->
<?php if (!empty($success_message)): ?>
    <p><?php echo $success_message; ?> <a href="viewgradetry.php">Return back to student list?</a></p>
<?php elseif (!empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

