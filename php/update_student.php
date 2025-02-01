<?php 
// Database connection details
require 'functions.php';
$conn = db_connect();

// Start session
session_start();

checkSessionTimeout();

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 3 && $_SESSION['role'] !=2)) { // Only Admin or Faculty
    header("Location: login.php");
    exit("Unauthorized access.");
}

$user_role = (int)$_SESSION['role']; // Cast to integer to match type
$user_id = $_SESSION['id'];

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $csrf_token_hashed = hash("sha256", $_SESSION['csrf_token']);
    $issued_at = date("Y-m-d H:i:s");
    $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Insert CSRF token into the database
    $admin_id = ($user_role === 3) ? $user_id : null;
    $staff_id = ($user_role === 2) ? $user_id : null;

    $stmt = $conn->prepare("INSERT INTO csrf (TOKEN, ISSUED_AT, EXPIRES_AT, ADMIN_ID, STAFF_ID) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $csrf_token_hashed, $issued_at, $expires_at, $admin_id, $staff_id);
    if (!$stmt->execute()) {
        die("Error inserting CSRF token: " . $stmt->error);
    }
    $stmt->close();
}

$student = null;
$student_courses = [];
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, name, phonenumber, email, faculty, department_id FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "<p>No student records found.</p>";
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT course_id FROM student_courses WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $student_courses[] = $row['course_id'];
    }
    $stmt->close();
}

// Fetch all available courses
$courses = $conn->query("SELECT id, name FROM course");
if (!$courses) {
    die("Error fetching courses: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['update'])) {
    $name = $_POST['name'];
    $phonenumber = $_POST['phonenumber'];
    $email = $_POST['email'];
    $faculty = $_POST['faculty'];
    $department_id = $_POST['department_id'];
    $selected_courses = $_POST['course_ids'] ?? [];

    $stmt = $conn->prepare("UPDATE students SET name=?, phonenumber=?, email=?, faculty=?, department_id=? WHERE id=?");
    $stmt->bind_param("ssssii", $_POST['name'], $_POST['phonenumber'], $_POST['email'], $_POST['faculty'], $_POST['department_id'], $_POST['id']);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Student details updated successfully.";
    } else {
        $_SESSION['message'] = "Update failed or no changes made: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM student_courses WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
    foreach ($selected_courses as $course_id) {
        $stmt->bind_param("ii", $student_id, $course_id);
        $stmt->execute();
    }
    $stmt->close();
    header("Location: update_student.php?id=$student_id");
    exit;
}
$conn->close();
?>
<!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content="width=device-width, initial-scale=1.0">
        <title> Robotic Management System</title>
    <style>
body{
    margin: 0;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color:#050a44;
    background-size: cover;
    }
    *{
    margin: 0;
    box-sizing: border-box;
    font-family: sans-serif;
    }
    form {
        width: 50%;
        margin: 30px auto;
        padding: 20px;
        border-radius: 10px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }
     /* Heading Style */
     h2{
    text-align: center;
    color: #2c2e3a;
    margin-top: 30px;
    margin-bottom: 20px;
    }
    /* Label Style */
    label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }
    /* Input and Select Styles */
    input[type="text"], input[type="tel"], input[type="email"], select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }
    select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .options label {
    margin-top: 20px;
    margin-bottom: 30px;
    font-size: 15px;
    color: #2c2e3a;
    }
    input[type="checkbox"]{
        padding: 10px;
        border: none;
        border-radius: 10px;
        background: transparent;
        border: 1px solid #2c2e3a;
        color: #141619;
        font-size: 13px;
        margin-bottom: 20px;
    }
.options input{
    margin-right: 5px;
    margin-top: 10px;
}
input[type="submit"] {
        display: block;
        width: 100%;
        background: #fff;
        color: black;
        padding: 10px;
        border: 1px solid #2c2e3a;
        border-radius: 10px;
        cursor: pointer;
        margin-top: 15px;
        text-align: center;
        font-size: 15px;
    }
    .back-button {
    border: none;
    outline: none;
    background-color:#050a44;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    width: 100px;
  
    }
    button:hover{
    margin-top: 0px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}

input[type="submit"]:hover {
    margin-top: 15px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}

</style>
</head>
<?php if (isset($_SESSION['message'])) : ?>
    <p style="color: #fff;"><?= $_SESSION['message']; ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>


<?php if (isset($student)) : ?>
    <div class="back-button">
    <a href="assignments.php?action=read"><button>Back</button></a>
</div>
    <form method="POST">
    <h2>Update Student Record</h2>
        <input type='hidden' name='id' value='<?= htmlspecialchars($student['id']) ?>'>
        <label>Name:</label><input type='text' name='name' value='<?= htmlspecialchars($student['name']) ?>'><br>
        <label>Phone Number:</label><input type='tel' name='phonenumber'pattern="\d{8}" title="Phone number must be 8 digits" value='<?= htmlspecialchars($student['phonenumber']) ?>'><br>
        <label>Email:</label><input type='text' name='email' value='<?= htmlspecialchars($student['email']) ?>'><br>
        
        <label for="faculty">Faculty:</label>
        <select id="faculty" name="faculty" required>
        <option value="ENG" <?= $student['faculty'] == 'ENG' ? 'selected' : '' ?>>Engineering</option>
        <option value="IIT" <?= $student['faculty'] == 'IIT' ? 'selected' : '' ?>>Informatics and IT</option>
        </select><br>

        <label for="department">Department:</label> 
        <select id="department" name="department_id" required>
        <option value="" disabled selected>Select</option>
        <option value="1">RBE/ENG</option>
        <option value="2">RBS/IIT</option>
        <option value="3">RMC/IIT</option>
        </select> <br>
    
        <label for="course">Assign Courses:</label><br>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <input type="checkbox" name="course_ids[]" value="<?= $course['id'] ?>" 
                <?= in_array($course['id'], $student_courses) ? 'checked' : '' ?>>
            <?= htmlspecialchars($course['name']) ?><br>
        <?php endwhile; ?>

        <input type='submit' name='update' value='Update Details'>
        <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
    </form>
<?php endif; ?>
