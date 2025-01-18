<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connection.php';

    $id = $_POST['id'];
    $score = $_POST['score'];
    $grade = $_POST['grade'];

    $stmt = $conn->prepare("UPDATE grades SET SCORE = ?, GRADE = ? WHERE ID = ?");
    $stmt->bind_param("ssi", $score, $grade, $id);

    if ($stmt->execute()) {
        echo "Grade successfully updated!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!-- Update Grades Form -->
<form method="POST">
    Grade ID: <input type="number" name="id" required><br>
    New Score: <input type="text" name="score" required><br>
    New Grade: <input type="text" name="grade" required><br>
    <input type="submit" value="Update Grade">
</form>
