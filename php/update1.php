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

// Start session
session_start();
require "functions.php";

checkSessionTimeout();

// Ensure only Admin (role = 3) and Faculty (role = 2) can access
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 3 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit("Unauthorized access.");
}

$user_role = (int)$_SESSION['role']; // Get the user's role (2 = Faculty, 3 = Admin)

// Generate CSRF token if not already set
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// Fetch course details for the specific course
$course_id = $_GET['id'] ?? null;
if (!$course_id || !is_numeric($course_id)) {
    die("Invalid course ID.");
}

$stmt = $conn->prepare("SELECT * FROM course WHERE ID = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();

if (!$course) {
    die("Course not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$token || $token !== $_SESSION['token']) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit("Invalid CSRF token.");
    }

    // Gather form inputs
    $name = $_POST['name'] ?? '';
    $code = $_POST['code'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? '';

    // Validate inputs
    if (empty($name) || empty($code) || empty($start_date) || empty($end_date)) {
        echo "<p style='color: red;'>Please fill in all fields.</p>";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        echo "<p style='color: red;'>Error: Start Date cannot be later than End Date.</p>";
    } else {
        // Update course details (name, code, start_date, end_date)
        $stmt = $conn->prepare("UPDATE course SET NAME = ?, CODE = ?, START_DATE = ?, END_DATE = ? WHERE ID = ?");
        $stmt->bind_param("ssssi", $name, $code, $start_date, $end_date, $course_id);

        if (!$stmt->execute()) {
            echo "<p style='color: red;'>Error updating course: " . $stmt->error . "</p>";
        }
        $stmt->close();

        // Only Faculty can update the status
        if ($user_role == 2 && in_array($status, ['start', 'in-progress', 'ended'])) {
            $stmt = $conn->prepare("UPDATE course SET STATUS = ? WHERE ID = ?");
            $stmt->bind_param("si", $status, $course_id);

            if ($stmt->execute()) {
                echo "<p style='color: green;'>Course status updated successfully.</p>";
            } else {
                echo "<p style='color: red;'>Error updating course status: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }

        // Redirect after success
        $_SESSION['message'] = "Course updated successfully.";
        header("Location: view_course.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Course</title>
</head>
<body>
    <h2>Update Course</h2>
    <form method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token']) ?>">

        <!-- Course Details -->
        Name: <input type="text" name="name" value="<?= htmlspecialchars($course['NAME']) ?>" required><br>
        Code: <input type="text" name="code" value="<?= htmlspecialchars($course['CODE']) ?>" required><br>
        Start Date: <input type="date" name="start_date" value="<?= htmlspecialchars($course['START_DATE']) ?>" required><br>
        End Date: <input type="date" name="end_date" value="<?= htmlspecialchars($course['END_DATE']) ?>" required><br>

        <!-- Status Dropdown -->
        <?php if ($user_role == 2): // Show status dropdown only for Faculty ?>
            Status:
            <select name="status" required>
                <option value="start" <?= $course['status'] == 'start' ? 'selected' : '' ?>>Start</option>
                <option value="in-progress" <?= $course['status'] == 'in-progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="ended" <?= $course['status'] == 'ended' ? 'selected' : '' ?>>Ended</option>
            </select><br>
        <?php else: ?>
            <!-- Display status as plain text for Admin -->
            Status: <?= htmlspecialchars($course['status']) ?><br>
        <?php endif; ?>

        <button type="submit">Update</button>
    </form>
    <a href="view_course.php">Back to Courses</a>
</body>
</html>
