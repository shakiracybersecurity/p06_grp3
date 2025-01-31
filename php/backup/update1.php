<?php 
// Database connection
require 'functions.php';
$conn = db_connect();

// Start session
session_start();

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
$departments = [];
$dept_stmt = $conn->prepare("SELECT NAME FROM department");
$dept_stmt->execute();
$dept_result = $dept_stmt->get_result();
while ($dept_row = $dept_result->fetch_assoc()) {
    $departments[] = $dept_row['NAME'];
}
$dept_stmt->close();
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
    $department_name = $_POST['department_name'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $status = $_POST['status'] ?? '';

    // Validate inputs
    if (empty($name) || empty($code) || empty($department_name) || empty($start_date) || empty($end_date)) {
        echo "<p style='color: red;'>Please fill in all fields.</p>";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        echo "<p style='color: red;'>Error: Start Date cannot be later than End Date.</p>";
    } else {
        // Update course details (name, code, department, start_date, end_date)
        $stmt = $conn->prepare("UPDATE course SET NAME = ?, CODE = ?, DEPARTMENT_NAME = ?, START_DATE = ?, END_DATE = ? WHERE ID = ?");
        $stmt->bind_param("sssssi", $name, $code, $department_name, $start_date, $end_date, $course_id);

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
    .container{
    margin-top: 0px;
    margin:50px auto;
    max-width: 500px;
    height: 550px;
    background-color: #fff;
    padding: 30px;
    box-shadow: 0 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    border: 1px solid #fff;
}

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
     input[type="text"], select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }
    input[type="date"], select{
    width: 300px; /* Adjust width */
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    color: #333;
    margin-bottom: 20px;
    }
    select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
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
      
    button:hover {
    margin-top: 15px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}
.back-button {
    border: none;
    outline: none;
    background-color:#050a44;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    width: 100px;
    display:flex;
  
    }
    a{
    text-decoration: none;
    }
    </style>
</head>
<div class="back-button">
<a href="view_course.php"><button>Back to Courses</button></a>
</div>
<body>
<div class="container">
    <form method="POST">
    <h2>Update Course</h2>
        <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['token']) ?>">

        <!-- Course Details -->
        Name: <input type="text" name="name" value="<?= htmlspecialchars($course['NAME']) ?>" required><br>
        Code: <input type="text" name="code" value="<?= htmlspecialchars($course['CODE']) ?>" required><br>
        Department: 
        <select name="department_name" required>
            <option value="">Select Department</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= htmlspecialchars($dept) ?>" 
                    <?= $dept == $course['DEPARTMENT_NAME'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept) ?>
                </option>
            <?php endforeach; ?>
        </select><br>
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
        </div>
    </form>
</body>
</html>
