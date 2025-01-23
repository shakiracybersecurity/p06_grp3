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

// Start session and check role
session_start();
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validate dates
    if (strtotime($start_date) > strtotime($end_date)) {
        $error = "Start date cannot be later than the end date.";
    } else {
        // Insert course into the database
        $sql = "INSERT INTO course (NAME, CODE, START_DATE, END_DATE) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $code, $start_date, $end_date);

        if ($stmt->execute()) {
            header("Location: view_course.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Course</title>
</head>
<body>
    <h2>Create Course</h2>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        Name: <input type="text" name="name" placeholder="Enter course name" required><br>
        Code: <input type="text" name="code" placeholder="Enter course code" required><br>
        Start Date: <input type="date" name="start_date" required><br>
        End Date: <input type="date" name="end_date" required><br>
        <button type="submit">Create Course</button>
    </form>
    <br>
    <a href="view_course.php">Back to View Courses</a>
</body>
</html>
<?php $conn->close(); ?>