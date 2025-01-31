<?php
function db_connect(){
    $host = 'localhost';
    $dbname = 'robotic course management'; // Updated for clarity
    $user = 'root';
    $pass = '';

    // Connect to the database
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// check if the user is logged in and role of user
function is_logged_in($allowed) {
    if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}elseif(!in_array($_SESSION['role'], $allowed)){
    header("Location: login.php");
    exit();
}}

function can_delete(){
    if ($_SESSION['role'] == 3){
        return TRUE;
    }else{
        return FALSE;
    }
}

function checkSessionTimeout($timeout_duration = 300) {
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        if ($inactive_time > $timeout_duration) {
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}

function registerStudent($post_data, $user_role){
    global $conn;

    if (!isset($post_data['token'])|| $post_data['token'] !== $_SESSION['csrf_token']){
        http_response_code(403);
        exit("Invalid CSRF token.");
    }
    if (!isset($post_data['name'], $post_data['phonenumber'], $post_data['email'], $post_data['department_id'], $post_data['id'], $post_data['faculty'])) {
        return "All fields are required.";
    }
    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $phonenumber = htmlspecialchars(trim($_POST['phonenumber']));
    $email = htmlspecialchars(trim($_POST['email']));
    $department_id = htmlspecialchars(trim($_POST['department_id']));
    $studentid = htmlspecialchars(trim($_POST['id']));
    $faculty = htmlspecialchars(trim($_POST['faculty']));
    $role_id = 1;  // Default role is 1 for students

    $course_ids = filter_input(INPUT_POST, 'course_ids', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY); // Courses for faculty only

    if (!preg_match('/^\d{8}$/', $phonenumber)) {
        return "Invalid phone number. It must be exactly 8 digits.<br>";
    } 

    // Check if the student ID already exists
    $check_stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $check_stmt->bind_param("s", $studentid);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        return "Error: A user with this ID already exists.";
    }

    // Register the student
    $register_stmt = $conn->prepare("INSERT INTO students (name, phonenumber, email, department_id, id, faculty, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $register_stmt->bind_param("ssssssi", $name, $phonenumber, $email, $department_id, $studentid, $faculty, $role_id);

    if ($register_stmt->execute()) {
        $registration_successful = true; // Mark as successful
        $message = "Registration for $name successful!<br>";

         // Only Faculty can assign courses
         if ($user_role == 2 && !empty($course_ids)) {
            $assign_stmt = $conn->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
            foreach ($course_ids as $course_id) {
                $assign_stmt->bind_param("ii", $studentid, $course_id);
                $assign_stmt->execute();
            }
            $assign_stmt->close();
            $message = "Courses assigned successfully!<br>";
        }
     return $message;
    }else{
        return"Error: " . $register_stmt->error;
    }
    $register_stmt->close();
    $check_stmt->close();
}

function getStudentRecords(){
    global $conn;

    $stmt = $conn->prepare("
    SELECT 
        students.id AS student_id, 
        students.name AS student_name, 
        students.phonenumber, 
        students.email, 
        students.faculty, 
        department.name AS department_name,
        GROUP_CONCAT(
            CONCAT(course.name, ' (', course.code, ')') 
            ORDER BY course.name SEPARATOR ', '
        ) AS course_names_with_codes, 
        GROUP_CONCAT(
            course.status 
            ORDER BY course.name SEPARATOR ', '
        ) AS course_statuses
    FROM students
    LEFT JOIN student_courses ON students.id = student_courses.student_id
    LEFT JOIN course ON student_courses.course_id = course.id
    LEFT JOIN department ON students.department_id = department.id
    GROUP BY students.id
");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

return $result;
}

function getCourseDetails($course_id){
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM course WHERE ID = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();

    return $course ?: null; // Return null if course not found
}

function getDepartments(){
    global $conn;
    $departments = [];
    
    $dept_stmt = $conn->prepare("SELECT NAME FROM department");
    $dept_stmt->execute();
    $dept_result = $dept_stmt->get_result();

    while ($dept_row = $dept_result->fetch_assoc()) {
        $departments[] = $dept_row['NAME'];
    }

    $stmt->close();
    return $departments;
}

function updateCourses($course_id, $post_data, $user_role){
    global $conn;

    if (!isset($post_data['token'])|| $post_data['token'] !== $_SESSION['csrf_token']){
        http_response_code(403);
        exit("Invalid CSRF token.");
    }

    // Gather form inputs
    $name = $post_data['name'] ?? '';
    $code = $post_data['code'] ?? '';
    $department_name = $post_data['department_name'] ?? '';
    $start_date = $post_data['start_date'] ?? '';
    $end_date = $post_data['end_date'] ?? '';
    $status = $post_data['status'] ?? '';

    // Validate inputs
    if (empty($name) || empty($code) || empty($department_name) || empty($start_date) || empty($end_date)) {
        return"<p style='color: white;'>Please fill in all fields.</p>";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        return "<p style='color: white;'>Error: Start Date cannot be later than End Date.</p>";
    } 

    // Update course details (name, code, department, start_date, end_date)
    $stmt = $conn->prepare("UPDATE course SET NAME = ?, CODE = ?, DEPARTMENT_NAME = ?, START_DATE = ?, END_DATE = ? WHERE ID = ?");
    $stmt->bind_param("sssssi", $name, $code, $department_name, $start_date, $end_date, $course_id);
    $stmt->execute();
    $stmt->close();

    // Only Faculty can update the status
    if ($user_role == 2 && in_array($status, ['start', 'in-progress', 'ended'])) {
        $stmt = $conn->prepare("UPDATE course SET STATUS = ? WHERE ID = ?");
        $stmt->bind_param("si", $status, $course_id);
        $stmt->execute();
        $stmt->close();
    }
    return "Course updated successfully.";
}

?>
