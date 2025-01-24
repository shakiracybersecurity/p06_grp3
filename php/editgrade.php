<?php
// Database connection
$host = 'localhost';
$dbname = 'robotic course management';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Get the grade ID from the URL
$grade_id = $_GET['id'] ?? '';
if (empty($grade_id)) {
    die("Grade ID is required.");
}

// Fetch student and grade details
$stmt = $conn->prepare("SELECT g.SCORE, g.GRADE, s.NAME AS student_name 
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_score = $_POST['score'] ?? 0;
    $new_grade = $_POST['grade'] ?? '';

    if (!empty($new_score) && !empty($new_grade)) {
        $update_stmt = $conn->prepare("UPDATE grades SET SCORE = ?, GRADE = ? WHERE ID = ?");
        $update_stmt->bind_param("isi", $new_score, $new_grade, $grade_id);
        if ($update_stmt->execute()) {
            $success_message = "Grade updated successfully!";
        } else {
            $error_message = "Failed to update grade.";
        }
        $update_stmt->close();
    } else {
        $error_message = "All fields are required.";
    }
}

$conn->close();
?>

<!-- Edit Grades Form -->
<style>
    /* Style for the form container */
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
    h3 {
        text-align: center;
        color: #4CAF50;
        font-size: 24px;
        margin-bottom: 20px;
    }

    /* Label Style */
    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }

    /* Input and Select Styles */
    input[type="number"], select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }

    /* Submit Button Style */
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

    /* Success or Error Message Style */
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
    <h3>You are editing <?php echo htmlspecialchars($grade['student_name']); ?>'s grade</h3>
    
    <label for="score">New Score:</label>
    <input type="number" step="1" name="score" id="score" min="0" max="100" 
           value="<?php echo htmlspecialchars($grade['SCORE']); ?>" required>

    <label for="grade">New Grade:</label>
    <select name="grade" id="grade" required>
        <option value="A" <?php echo $grade['GRADE'] == 'A' ? 'selected' : ''; ?>>A</option>
        <option value="B+" <?php echo $grade['GRADE'] == 'B+' ? 'selected' : ''; ?>>B+</option>
        <option value="B" <?php echo $grade['GRADE'] == 'B' ? 'selected' : ''; ?>>B</option>
        <option value="C+" <?php echo $grade['GRADE'] == 'C+' ? 'selected' : ''; ?>>C+</option>
        <option value="C" <?php echo $grade['GRADE'] == 'C' ? 'selected' : ''; ?>>C</option>
        <option value="D+" <?php echo $grade['GRADE'] == 'D+' ? 'selected' : ''; ?>>D+</option>
        <option value="D" <?php echo $grade['GRADE'] == 'D' ? 'selected' : ''; ?>>D</option>
        <option value="F" <?php echo $grade['GRADE'] == 'F' ? 'selected' : ''; ?>>F</option>
    </select>
    
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
    <button type="submit">Update Grade</button>
</form>

<!-- Confirmation or Error Messages -->
<?php if (!empty($success_message)): ?>
    <p><?php echo $success_message; ?> <a href="viewgrade.php">Return back to student list?</a></p>
<?php elseif (!empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

