<?php

$host = 'localhost';
$dbname = 'robotic course management'; // Updated for clarity
$user = 'root';
$pass = '';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();

require 'functions.php';
is_logged_in([3, 2]);

if ($_SESSION['role'] == 3) { // Redirect back to dashboard of role
    $redirect = "admin_dashboard.php";
} elseif ($_SESSION['role'] == 2) {
    $redirect = "faculty_dashboard.php";
}

$stmt = $conn->prepare("SELECT g.ID, SCORE, s.NAME AS student_name, c.NAME AS course_name, GRADE 
                        FROM grades g
                        LEFT JOIN students s ON g.STUDENT_ID = s.ID
                        LEFT JOIN course c ON g.COURSE_ID = c.ID");
                       // JOIN 
                       // JOIN ");   

if (!$stmt) {
    die("Query failed: " . $conn->error);
}
$stmt -> execute();
$result = $stmt -> get_result();
$grade = $result->fetch_all(MYSQLI_ASSOC);
$stmt -> close();

?>

<a href="<?php echo $redirect; ?>">back</a> <br>
<br>
<table>
    <thead>
        <tr>
            <th>id</th>
            <th>student_name</th>
            <th>course_name</th>
            <th>score</th>
            <th>grade</th>
            <th>action</th>
        </tr>
    </thead>  
    <tbody>
        <?php 
        $count = 0;
        foreach ($grade as $grade): ?>
            <tr>
                <td><?php echo ($count += 1); ?></td>
                <td><?php echo htmlspecialchars($grade['student_name']); ?></td>
                <td><?php echo htmlspecialchars($grade['course_name']); ?></td>
                <td><?php echo htmlspecialchars($grade['SCORE']); ?></td>
                <td><?php echo htmlspecialchars($grade['GRADE']); ?></td>
                <td>
                    <a href="editgrade.php?id=<?php echo $grade['ID']; ?>">edit</a> 
                    <?php if (can_delete()): ?>
                    | <a href="deletegrade.php?id=<?php echo $grade['ID']; ?>">delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>




