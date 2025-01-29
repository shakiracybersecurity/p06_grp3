<?php
// Database connection
require 'functions.php';
$conn = db_connect();
// Start session and check role
session_start();

checkSessionTimeout();

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit();
}

// Fetch all courses
$sql = "SELECT ID, NAME, CODE, START_DATE, END_DATE, STATUS FROM course";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<body>
    <h2>Courses Available</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Code</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['ID'] ?></td>
                <td><?= htmlspecialchars($row['NAME']) ?></td>
                <td><?= htmlspecialchars($row['CODE']) ?></td>
                <td>
                    <?= ($row['START_DATE'] !== '0000-00-00') 
                        ? date("d-m-Y", strtotime($row['START_DATE'])) 
                        : 'N/A'; ?>
                </td>
                <td>
                    <?= ($row['END_DATE'] !== '0000-00-00') 
                        ? date("d-m-Y", strtotime($row['END_DATE'])) 
                        : 'N/A'; ?>
                </td>
                <td>
                    <?= htmlspecialchars($row['STATUS']) ?></td>
                <td>
                    <a href="update1.php?id=<?= $row['ID'] ?>">Edit</a>
                    <?php if ($_SESSION['role'] == 3): // Only Admin can delete ?>
                        | <a href="delete_course.php?id=<?= $row['ID'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table><br>
    <!-- Create Course Button -->
    <a href="create_course.php" style="display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Create Course</a>    
    <br></br>
    <a href="admin_dashboard.php">Back</a>
</body>
</html>
<?php $conn->close(); ?>