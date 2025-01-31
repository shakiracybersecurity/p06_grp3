<?php
// Database connection
require 'functions.php';
$conn = db_connect();
session_start();

checkSessionTimeout();

// Generate and store a new CSRF token if it doesn't exist
if (empty($_SESSION['csrf_plain'])) {
    $_SESSION['csrf_plain'] = bin2hex(random_bytes(32)); // Store plain token
    $_SESSION['csrf_hash'] = password_hash($_SESSION['csrf_plain'], PASSWORD_DEFAULT); // Store hashed token
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
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    
    // Validate CSRF token
    if (!$token || !password_verify($token, $_SESSION['csrf_hash'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }

    // CSRF token is valid - Now unset it to prevent reuse
    unset($_SESSION['csrf_plain']);
    unset($_SESSION['csrf_hash']);

    // Regenerate new CSRF token for next request
    $_SESSION['csrf_plain'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_hash'] = password_hash($_SESSION['csrf_plain'], PASSWORD_DEFAULT);

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
    input[type="number"], select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }
    
    select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
    /* Submit Button Style */
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

a{
    text-decoration: none;
}
    
    button:hover {
    margin-top: 15px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
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
<div class="back-button">
<a href="viewgradetry.php"><button>Back</button></a>
</div>
<form method="POST">
    <h2>You are editing <?php echo htmlspecialchars($grade['student_name']); ?>'s grade</h2>
    
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
    
    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_plain'] ?? '') ?>">
    <button type="submit">Update Grade</button>
</form>

<!-- Confirmation or Error Messages -->
<?php if (!empty($success_message)): ?>
    <p><?php echo $success_message; ?> <a href="viewgradetry.php">Return back to student list?</a></p>
<?php elseif (!empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

