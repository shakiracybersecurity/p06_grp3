<?php
// Start session and include database connection
session_start();
require 'functions.php';
$conn = db_connect();

// Check if the user is logged in and has the appropriate role (faculty or admin)
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit();
}

// Handle form submission to assign courses
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_courses'])) {
    $student_id = intval($_POST['student_id']);
    $course_ids = $_POST['course_ids']; // Array of selected course IDs

    // Validate inputs
    if (empty($student_id) || empty($course_ids)) {
        echo "<script>
            alert('Please select a student and at least one course.');
            window.location.href = 'assign_course.php';
        </script>";
        exit();
    }

    // Assign selected courses to the student
    $stmt = $conn->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
    foreach ($course_ids as $course_id) {
        $stmt->bind_param("ii", $student_id, $course_id);
        $stmt->execute();
    }
    $stmt->close();

    echo "<script>
        alert('Courses assigned successfully.');
        window.location.href = 'view_assignments.php';
    </script>";
    exit();
}

// Fetch students for the dropdown
$students_result = $conn->query("SELECT id, name FROM students");

// Fetch courses for the checkboxes
$courses_result = $conn->query("SELECT id, name FROM course");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Courses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        select, input[type="checkbox"] {
            margin-bottom: 15px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Assign Courses to Student</h2>
        <form method="POST" action="">
            <!-- Dropdown to select a student -->
            <label for="student_id">Select Student:</label><br>
            <select name="student_id" id="student_id" required>
                <option value="">-- Select Student --</option>
                <?php while ($student = $students_result->fetch_assoc()): ?>
                    <option value="<?php echo $student['id']; ?>">
                        <?php echo $student['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br><br>

            <!-- Checkboxes to select courses -->
            <label>Select Courses:</label><br>
            <?php while ($course = $courses_result->fetch_assoc()): ?>
                <input type="checkbox" name="course_ids[]" value="<?php echo $course['id']; ?>">
                <?php echo $course['name']; ?><br>
            <?php endwhile; ?>
            <br>

            <!-- Submit Button -->
            <input type="submit" name="assign_courses" value="Assign Courses">
        </form>
    </div>
</body>
</html>
