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

// Fetch all courses
$sql = "SELECT * FROM course";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Courses</title>
</head>
<body>
    <h2>View Courses</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Code</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['ID'] ?></td>
                <td><?= htmlspecialchars($row['NAME']) ?></td>
                <td><?= htmlspecialchars($row['CODE']) ?></td>
                <td><?= $row['START_DATE'] ?></td>
                <td><?= $row['END_DATE'] ?></td>
                <td>
                    <a href="update_course.php?id=<?= $row['ID'] ?>">Edit</a>
                    <?php if ($_SESSION['role'] == 3): // Only Admin can delete ?>
                        | <a href="delete_course.php?id=<?= $row['ID'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
<?php $conn->close(); ?>
