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

// Fetch course details
$id = $_GET['id'];
$sql = "SELECT * FROM course WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Update course
    $update_sql = "UPDATE course SET NAME = ?, CODE = ?, START_DATE = ?, END_DATE = ? WHERE ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $name, $code, $start_date, $end_date, $id);

    if ($update_stmt->execute()) {
        header("Location: view_course.php");
        exit();
    } else {
        echo "Error: " . $update_stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Course</title>
</head>
<body>
    <h2>Update Course</h2>
    <form method="POST">
        Name: <input type="text" name="name" value="<?= htmlspecialchars($course['NAME']) ?>" required><br>
        Code: <input type="text" name="code" value="<?= htmlspecialchars($course['CODE']) ?>" required><br>
        Start Date: <input type="date" name="start_date" value="<?= $course['START_DATE'] ?>" required><br>
        End Date: <input type="date" name="end_date" value="<?= $course['END_DATE'] ?>" required><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
<?php $conn->close(); ?>
