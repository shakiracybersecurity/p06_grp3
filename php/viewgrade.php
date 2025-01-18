<?php
include 'db_connection.php'; // Include your DB connection file.

$result = $conn->query("SELECT g.ID, s.NAME AS student_name, c.NAME AS course_name, g.SCORE, g.GRADE 
                        FROM grades g
                        JOIN students s ON g.STUDENT_ID = s.ID
                        JOIN course c ON g.COURSE_ID = c.ID");

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Student</th><th>Course</th><th>Score</th><th>Grade</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['ID']}</td>
        <td>{$row['student_name']}</td>
        <td>{$row['course_name']}</td>
        <td>{$row['SCORE']}</td>
        <td>{$row['GRADE']}</td>
    </tr>";
}
echo "</ta