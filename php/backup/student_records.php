<?php  
// Database connection details
require 'functions.php';

// Start session
session_start();

checkSessionTimeout();

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 3 && $_SESSION['role'] != 2)) { // Only Admin or Faculty can access
    header("Location: login.php");
    exit("Unauthorized access.");
}

// Generate a CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION['csrf_token'];

// Query to get student records
$query = "
    SELECT 
        students.id AS student_id, 
        students.name AS student_name, 
        students.phonenumber, 
        students.email, 
        students.faculty, 
        COALESCE(GROUP_CONCAT(course.name SEPARATOR ', '), 'No courses assigned') AS course_names, 
        COALESCE(department.name, 'No department assigned') AS department_name
    FROM students
    LEFT JOIN student_courses ON students.id = student_courses.student_id
    LEFT JOIN courses ON student_courses.course_id = courses.id
    LEFT JOIN department ON students.department_id = department.id
    GROUP BY students.id
";

$result = $conn->query($query);
if ($result->num_rows > 0) {
echo "<a href=\"" . $redirect . "\">Back</a>";

    echo "<h1>Current Student Records</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
        <th>ID</th><th>Name</th><th>Phone Number</th><th>Email</th><th>Faculty</th><th>Courses</th><th>Department</th>
        <th>Actions</th>
    </tr>";

    while ($student = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($student['student_id']) . "</td>";
        echo "<td>" . htmlspecialchars($student['student_name']) . "</td>";
        echo "<td>" . htmlspecialchars($student['phonenumber']) . "</td>";
        echo "<td>" . htmlspecialchars($student['email']) . "</td>";
        echo "<td>" . htmlspecialchars($student['faculty']) . "</td>";
        echo "<td>" . htmlspecialchars($student['course_names']) . "</td>";
        echo "<td>" . htmlspecialchars($student['department_name']) . "</td>";
        echo "<td>
            <a href='update_student.php?id=" . htmlspecialchars($student['student_id']) . "'>Update</a>
            <form method='POST' action='delete_student.php' onsubmit=\"return confirm('Are you sure you want to delete this record?');\" style='display:inline;'>
                <input type='hidden' name='id' value='" . htmlspecialchars($student['student_id']) . "'>
                <input type='hidden' name='token' value='" . htmlspecialchars($csrf_token) . "'>
                <input type='submit' name='delete' value='Delete'>
            </form>
            
        </td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No student records found.</p>";
}

$conn->close();

// Redirect based on role
if ($_SESSION['role'] == 3) {
    $redirect = "admin_dashboard.php";
} elseif ($_SESSION['role'] == 2) {
    $redirect = "faculty_dashboard.php";
}
?>

<a href="<?php echo $redirect; ?>">Back</a>
