<?php
// Database connection variables
$host = 'localhost';
$dbname = 'robotic course management';
$user = 'root';
$pass = '';

// Connect to the database using MySQLi
$conn = new mysqli($host, $user, $pass, $dbname);

// Check if the connection to the database was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start a session to manage user authentication
session_start();

// Ensure only logged-in users with the correct roles can access the script
if (!isset($_SESSION['username'])) { // If the user is not logged in
    header("Location: login.php");   // Redirect to the login page
    exit();                          // Stop further execution
} elseif ($_SESSION['role'] != 2 && $_SESSION['role'] != 3) { // If the user is neither Faculty (role 2) nor Admin (role 3)
    header("Location: login.php");   // Redirect to the login page
    exit();
}

// Function to create a new course
function createCourse($name, $classId, $startDate, $endDate) {
    global $conn; // Use the global database connection
    $stmt = $conn->prepare("INSERT INTO course (NAME, START_DATE, END_DATE, STAFF_ID, CLASS_ID, STUDENT_ID) VALUES (?, ?, ?, NULL, ?, NULL)");
    $stmt->bind_param("sssi", $name, $startDate, $endDate, $classId); // Bind parameters securely to prevent SQL injection
    if ($stmt->execute()) {
        echo "Course created successfully."; // Confirm successful creation
    } else {
        echo "Error: " . $stmt->error; // Display an error if something goes wrong
    }
    $stmt->close(); // Close the statement to free up resources
}

// Function to retrieve and display all courses
function readCourses() {
    global $conn; // Use the global database connection
    $result = $conn->query("SELECT * FROM course"); // Execute a query to fetch all courses
    if ($result->num_rows > 0) { // Check if any courses exist
        while ($row = $result->fetch_assoc()) { // Loop through the courses
            echo "ID: " . $row['ID'] . " - Name: " . $row['NAME'] . " - Start: " . $row['START_DATE'] . " - End: " . $row['END_DATE'] . " - Class ID: " . $row['CLASS_ID'] . "<br>";
        }
    } else {
        echo "No courses found."; // Message if no courses exist
    }
}

// Function to update course details
function updateCourse($id, $name, $classId, $startDate, $endDate) {
    global $conn; // Use the global database connection
    $stmt = $conn->prepare("UPDATE course SET NAME = ?, START_DATE = ?, END_DATE = ?, CLASS_ID = ? WHERE ID = ?");
    $stmt->bind_param("sssii", $name, $startDate, $endDate, $classId, $id); // Bind parameters securely
    if ($stmt->execute()) {
        echo "Course updated successfully."; // Confirm successful update
    } else {
        echo "Error: " . $stmt->error; // Display an error if something goes wrong
    }
    $stmt->close();
}

// Function to delete a course
function deleteCourse($id) {
    global $conn; // Use the global database connection
    $stmt = $conn->prepare("DELETE FROM course WHERE ID = ?"); // Delete course based on its ID
    $stmt->bind_param("i", $id); // Bind the ID parameter
    if ($stmt->execute()) {
        echo "Course deleted successfully."; // Confirm successful deletion
    } else {
        echo "Error: " . $stmt->error; // Display an error if something goes wrong
    }
    $stmt->close(); // Close the statement
}
?>

<!-- Form to create a course -->
<form method="POST">
    <h3>Create Course</h3>
    course name: <input type="text" name="course_name" required><br>

    start date: <input type="date" name="start_date" required><br>
    end date: <input type="date" name="end_date" required><br>

    class ID:
    <select name="class_id" id="class_id">
    <?php
        // Fetch class IDs and names for the dropdown
        $stmt = $conn->prepare("SELECT ID, NAME FROM class"); // Replace 'class' with the actual table name if different
        $stmt->execute();
        $result = $stmt->get_result();
        $classes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        foreach ($classes as $class): ?>
            <option value="<?php echo $class['ID']; ?>"><?php echo $class['NAME']; ?></option>
        <?php endforeach; ?>
    </select><br>

    <button type="submit" name="create_course">Create Course</button></form>

<?php
// Handle form submission for course creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_course'])) {
    $course_name = $_POST['course_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $class_id = $_POST['class_id'];

    // Insert the new course into the database
    $stmt = $conn->prepare("INSERT INTO course (NAME, START_DATE, END_DATE, CLASS_ID, STAFF_ID, STUDENT_ID) VALUES (?, ?, ?, ?, NULL, NULL)");
    $stmt->bind_param("sssi", $course_name, $start_date, $end_date, $class_id);

    if ($stmt->execute()) {
        echo "Course created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
