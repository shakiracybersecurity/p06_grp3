<?php
// Database connection details
$host = 'localhost';
$dbname = 'robotic course management';
$user = 'root';  // Replace with your MySQL username
$pass = '';      // Replace with your MySQL password

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$current_student = [];

// Step 1: Fetch student details based on search input
if (isset($_POST['search_student_id'])){
    $search_student_id = intval($_POST['search_student_id']);
    $stmt = $conn->prepare("SELECT id, name, phonenumber, email, course_id, faculty,department_id,class FROM students WHERE id = ?");
    if (!$stmt){
        die("Query preparation failed: " .$conn->error);
    }

    $stmt->bind_param("i", $search_student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows >0){
        echo"<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Name</th><th>Phone Number</th><th>Email</th><th>Faculty</th><th>Class</th>";

    //Display each record
    while ($student = $result->fetch_assoc()){
        echo "<tr>";
        echo "<td>" . htmlspecialchars($student['id']) . "</td>";
        echo "<td>" . htmlspecialchars($student['name']) . "</td>";
        echo "<td>" . htmlspecialchars($student['phonenumber']) . "</td>";
        echo "<td>"  . htmlspecialchars($student['email'])."</td>";
        echo "<td>"  . htmlspecialchars($student['faculty'])."</td>";
        echo "<td>"  . htmlspecialchars($student['class'])."</td>";
    }
    echo "</table>";
 } else{
    echo"<p>No student records found.</p>";
 }
 $stmt->close();
}

// Step 2: Update student details
if (isset($_POST['update_student'])) {
    $id = intval($_POST['id']);
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phonenumber = htmlspecialchars(trim($_POST['phonenumber']));
    $department_id = intval($_POST['department_id']);
    $course_id = intval($_POST['course_id']);
    $faculty = htmlspecialchars(trim($_POST['faculty']));

    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, phonenumber = ?, department_id = ?, course_id = ?, faculty = ? WHERE id = ?");
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("sssiiis", $name, $email, $phonenumber, $department_id, $course_id, $faculty, $id);

    if ($stmt->execute()) {
        $message = "Student details successfully updated!";
    } else {
        $message = "Error updating student details: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student</title>
</head>
<body>
    <h1>Manage Student Record</h1>
    

    <!-- Display message -->
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Search Form -->
    <form method="POST">
        <label for="search_student_id">Enter Student ID:</label>
        <input type="text" id="search_student_id" name="search_student_id" required>
        <input type="submit" value="Search">
    </form>

    <?php if (!empty($current_student)): ?>
        <!-- Update Form -->
        <h2>Update Student Details</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($current_student['id']) ?>">

            Name: <input type="text" name="name" value="<?= htmlspecialchars($current_student['name']) ?>" required><br>
            Email: <input type="email" name="email" value="<?= htmlspecialchars($current_student['email']) ?>" required><br>
            Phone Number: <input type="tel" name="phonenumber" value="<?= htmlspecialchars($current_student['phonenumber']) ?>" required><br>

            <label for="department">Department:</label>
            <select name="department_id" required>
                <option value="1" <?= $current_student['department_id'] == 1 ? 'selected' : '' ?>>RBE/ENG</option>
                <option value="2" <?= $current_student['department_id'] == 2 ? 'selected' : '' ?>>RBS/IIT</option>
                <option value="3" <?= $current_student['department_id'] == 3 ? 'selected' : '' ?>>RMC/IIT</option>
            </select><br>

            <label for="course">Course:</label>
            <select name="course_id" required>
                <option value="1" <?= $current_student['course_id'] == 1 ? 'selected' : '' ?>>Robotic Engineering</option>
                <option value="2" <?= $current_student['course_id'] == 2 ? 'selected' : '' ?>>Robotic Systems</option>
                <option value="3" <?= $current_student['course_id'] == 3 ? 'selected' : '' ?>>Robotic Mechanics and Control</option>
            </select><br>

            <label for="faculty">Faculty:</label>
            <select name="faculty" required>
                <option value="ENG" <?= $current_student['faculty'] == 'ENG' ? 'selected' : '' ?>>Engineering</option>
                <option value="IIT" <?= $current_student['faculty'] == 'IIT' ? 'selected' : '' ?>>Informatics and IT</option>
            </select><br>

            <input type="submit" name="update_student" value="Update">
        </form>
    <?php endif; ?>
</body>
</html>
