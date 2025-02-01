<?php  
// Database connection details
require 'functions.php';
$conn = db_connect();

// Start session
session_start();

checkSessionTimeout();
is_logged_in([3,2]);
// Restrict access: Only Admin (role = 3) and Faculty (role = 2) can access

$user_id = $_SESSION['id'];
$user_role = (int)$_SESSION['role']; // Cast role to integer

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'read';
if ($action == "create") {
    //Check if the user is a faculty member
    if ($_SESSION['role'] != 2) {
        echo "<script>
            alert('Access denied. Only faculty members can assign courses.');
            window.location.href = 'view_assignments.php'; // Redirect to another page (e.g., homepage or dashboard)
        </script>";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_courses'])) {
        $student_id = intval($_POST['student_id']);
        $course_ids = $_POST['course_ids'] ?? [];

        if (empty($student_id)) {
            echo "<script>alert('Please select a student.');</script>";
        } elseif (empty($course_ids)) {
            echo "<script>alert('Please select at least one course.');</script>";
        } else {
            $message = assignCoursesToStudent($student_id, $course_ids, $conn);
            echo "<script>
                alert('$message');
                window.location.href = 'view_assignments.php';
            </script>";
            exit();
        }
    }

    $students_result = $conn->query("SELECT id, name FROM students");
    if (!$students_result) {
        die("Error fetching students: " . $conn->error);
    }

    $courses_result = $conn->query("SELECT id, name FROM course");
    if (!$courses_result) {
        die("Error fetching courses: " . $conn->error);
    }
}

// Fetch student records for display (only if action = read)
$student_records = null; // Initialize variable to avoid undefined variable error


if ($action == 'read') {
    $student_records = getStudentRecords();
}

//Handle Update Course
$update_message = " ";
if ($action == 'update'){
    $course_id = $_GET['id'] ?? null;

    if(!$course_id || !is_numeric($course_id)){
        die("Invalid course ID.");
    }

    $course = getCourseDetails($course_id);
    if(!$course){
        die("Course not found.");
    }

    //Handle form submission for updating the course
    
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update_message = updateCourses($course_id, $_POST, $user_role);
    if (!empty($update_message)) {
        echo "<script>
            alert('$update_message');
            window.location.href = 'view_course.php'; // Redirect to the courses page
        </script>";
    }
}


    $departments = getDepartments(); //Fetch departments

}

?>


 <!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content="width=device-width, initial-scale=1.0">
        <title> Robotic Management System</title>
<?php if ($action == 'create'):?>
 <style>
    body{
    margin: 0;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color:#2c2e3a;
    background-size: cover;
}
*{
    margin: 0;
    box-sizing: border-box;
    font-family: sans-serif;
}
.container{
    margin-top: 0px;
    margin:50px auto;
    max-width: 500px;
    height: 500px;
    background-color: #fff;
    padding: 30px;
    box-shadow: 0 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    border: 1px solid #fff;
}
h1{
    text-align: center;
    color: #050A44;
    margin-top: 30px;
    margin-bottom: 20px;
}
form{
    display: flex;
    flex-direction: column;
    margin-top: 20px;
}
label{
        font-size: 15px;
        margin-bottom: 2px;
    }
input[type="text"],
input[type="email"], input[type="tel"]{
    padding: 10px;
    margin-top: 8px;
    border: none;
    border-radius: 15px;
    background: transparent;
    border: 1px solid #2c2e3a;
    color: #141619;
    font-size: 15px;
}
.options label {
        margin-top: 20px;
        margin-bottom: 30px;
        font-size: 15px;
        color: #2c2e3a;
}
input{
        padding: 10px;
        border: none;
        border-radius: 10px;
        background: transparent;
        border: 1px solid #2c2e3a;
        color: #141619;
        font-size: 13px;
        margin-top: 20px;
        margin-bottom: 20px;
}
input[type="checkbox"],{
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
select{
    width: 300px; /* Adjust width */
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    color: #333;
    margin-bottom: 20px;
}
select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

button {
    background: #fff;
    color: black;
    padding: 10px;
    border: 1px solid #2c2e3a;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 15px;
    display:flex;
}
button:hover {
    margin-top: 15px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}
   a {
        text-decoration: none;
    }
    input[type="submit"], button {
    background: #fff;
    color: black;
    padding: 10px;
    border: 1px solid #2c2e3a;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 15px;
    display:flex;
    }
    input[type="submit"]:hover, button:hover {
    margin-top: 15px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
    }
    </style>
<?php endif; ?>
<?php if ($action == 'read'): ?>
    <style>
    *{
    margin: 0;
    box-sizing: border-box;
    font-family: sans-serif;
}
    body{
    margin: 0;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color:#b3b4bd; 
    background-size: cover;
    
}
    table {
        border-collapse: collapse;
        width: 80%; /* Adjust width as needed */
        max-width: 1000px; /* Optional: limit table width */
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        margin-left: auto;
        margin-right: auto;
        }
    th, td{
        padding: 15px;
        text-align: center;
        border: 1px solid;
    }
    h2{
    text-align: center;
    color: #2c2e3a;
    margin-top: 30px;
    margin-bottom: 20px;
    }
    th{
        background-color: #0a21c0;
         color: white;
    }
    button, input[type="submit"]{
    background: #fff;
    color: black;
    padding: 10px;
    border: 1px solid #2c2e3a;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 0px;
    border: none;
    }
    button:hover,input[type="submit"]:hover {
    margin-top: 0px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}

    </style>  


<?php endif; ?>
<?php if ($action == 'update'): ?>
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
    .container{
    margin-top: 0px;
    margin:50px auto;
    max-width: 500px;
    height: 600px;
    background-color: #fff;
    padding: 30px;
    box-shadow: 0 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    border: 1px solid #fff;
}

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
     input[type="text"], select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
    }
    input[type="date"], select{
    width: 300px; /* Adjust width */
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    color: #333;
    margin-bottom: 20px;
    }
    select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
    button {
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
      
    button:hover {
    margin-top: 15px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}
.back-button {
    border: none;
    outline: none;
    background-color:#050a44;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    width: 100px;
    display:flex;
  
    }
    a{
    text-decoration: none;
    }
    </style>
 <?php endif; ?>   
    </style>
</head>
<body>
<!-- Display Student Records -->
<?php if ($action == 'read'): ?>
    <a href="<?= $user_role == 2 ? 'faculty_dashboard.php' : 'admin_dashboard.php' ?>"><button>Back</button></a>
    <h2>Current Student Records</h2>

    <?php if ($student_records && $student_records->num_rows > 0): ?>
        <table border='1' cellpadding='10'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Faculty</th>
                <th>Courses</th>
                <th>Statuses</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>

            <?php while ($student = $student_records->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($student['student_id']); ?></td>
                    <td><?= htmlspecialchars($student['student_name']); ?></td>
                    <td><?= htmlspecialchars($student['phonenumber']); ?></td>
                    <td><?= htmlspecialchars($student['email']); ?></td>
                    <td><?= htmlspecialchars($student['faculty']); ?></td>
                    <td><?= htmlspecialchars($student['course_names_with_codes']); ?></td>
                    <td><?= htmlspecialchars($student['course_statuses']); ?></td>
                    <td><?= htmlspecialchars($student['department_name']); ?></td>
                    <td>
                        <a href='update_student.php?id=<?= htmlspecialchars($student['student_id']); ?>'><button>Update</button></a>
                        <form method='POST' action='delete_student.php' onsubmit="return confirm('Are you sure you want to delete this record?');">
                            <input type='hidden' name='id' value='<?= htmlspecialchars($student['student_id']); ?>'>
                            <input type='hidden' name='token' value='<?= htmlspecialchars($_SESSION['csrf_token']); ?>'>
                            <input type='submit' name='delete' value='Delete'>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No student records found.</p>
    <?php endif; ?>
<?php endif; ?>

<?php if ($action == 'create'): ?>
    <a href="<?= $user_role == 2 ? 'faculty_dashboard.php' : 'admin_dashboard.php' ?>"><button>Back</button></a>
    <form method="POST">
        <div class="container">
            <h2>Assign Courses to Student</h2>
            
            <!-- Input field with a datalist for searching students -->
            <label for="student_id">Search and Select a Student by Name:</label><br>
            <input list="students" name="student_id" id="student_id" required placeholder="Type to search for a student">
            <datalist id="students">
                <?php while ($student = $students_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($student['id']); ?>">
                        <?php echo htmlspecialchars($student['name']); ?> (ID: <?php echo htmlspecialchars($student['id']); ?>)
                    </option>
                <?php endwhile; ?>
            </datalist>
            <br><br>

            <!-- Checkboxes for courses -->
            <div class="options">
                <label>Select Courses:</label><br>
                <?php while ($course = $courses_result->fetch_assoc()): ?>
                    <label>
                        <input type="checkbox" name="course_ids[]" value="<?php echo htmlspecialchars($course['id']); ?>">
                        <?php echo htmlspecialchars($course['name']); ?>
                    </label><br>
                <?php endwhile; ?>
                <br>
            </div>

            <!-- Submit Button -->
            <input type="submit" name="assign_courses" value="Assign Courses" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
        </div>
    </form>
<?php endif; ?>

<?php if ($action == 'update'): ?>
<div class="back-button">
<a href="view_course.php"><button>Back to Courses</button></a>
</div>
<body>
<div class="container">
    <form method="POST">
    <h2>Update Course</h2>
    <input type="hidden" name="token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <!-- Course details -->
        Name: <input type="text" name="name" value="<?= htmlspecialchars($course['NAME']) ?>" required><br>
        Code: <input type="text" name="code" value="<?= htmlspecialchars($course['CODE']) ?>" required><br>
        Department: 
        <select name="department_name" required>
            <option value="">Select Department</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= htmlspecialchars($dept) ?>" 
                    <?= $dept == $course['DEPARTMENT_NAME'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dept) ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        Start Date: <input type="date" name="start_date" value="<?= htmlspecialchars($course['START_DATE']) ?>" required><br>
        End Date: <input type="date" name="end_date" value="<?= htmlspecialchars($course['END_DATE']) ?>" required><br>

        <!-- Status Dropdown -->
        <?php if ($user_role == 2): // Show status dropdown only for Faculty ?>
            Status:
            <select name="status" required>
                <option value="start" <?= $course['status'] == 'start' ? 'selected' : '' ?>>Start</option>
                <option value="in-progress" <?= $course['status'] == 'in-progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="ended" <?= $course['status'] == 'ended' ? 'selected' : '' ?>>Ended</option>
            </select><br>
        <?php else: ?>
            <!-- Display status as plain text for Admin -->
            Status: <?= htmlspecialchars($course['status']) ?><br>
        <?php endif; ?>

        <button type="submit">Update</button>
        </div>
    </form>
</body>
</html>
<?php endif; ?>