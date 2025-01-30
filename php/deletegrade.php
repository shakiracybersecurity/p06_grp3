<?php
// Database connection
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
    body{
    margin: 0;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color:#050a44;
    background-size: cover;
    }
    *{
    margin: 0;
    box-sizing: border-box;
    font-family: sans-serif;
    }
    form {
        width: 50%;
        margin: 30px auto;
        padding: 20px;
        border-radius: 10px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }
     /* Heading Style */
     h2{
    text-align: center;
    color: #2c2e3a;
    margin-top: 30px;
    margin-bottom: 20px;
    }
    /* Label Style */
    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }
    /* Input and Select Styles */
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
        background: #fff;
        color: black;
        padding: 10px;
        border: 1px solid #2c2e3a;
        border-radius: 10px;
        cursor: pointer;
        margin-top: 15px;
        text-align: center;
        font-size: 15px;
    }
    .back-button {
    border: none;
    outline: none;
    background-color:#050a44;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    width: 100px;
  
    }
    button:hover {
    margin-top: 15px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
    }

    p {
        text-align: center;
        font-size: 16px;
        margin-top: 20px;
    }

    p a {
        color:#fff;
        text-decoration: none;
        font-weight: bold;
    }

    p a:hover {
        text-decoration: underline;
    }
    a{
    text-decoration: none;
    }
</style>
<div class ="back-button">
<a href="viewgradetry.php"><button>Back</button>
</div>
<form method="POST">
    <h2>You are deleting <?php echo htmlspecialchars($grade['student_name']); ?>'s grade</h2>
    
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

