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

// Start session
session_start();

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 3 && $_SESSION['role'] !=2)) { // Only Admin can delete
    header("Location: login.php");
    exit("Unauthorized access.");
}

$user_role = (int)$_SESSION['role']; // Cast to integer to match type
$user_id = $_SESSION['id'];

//Generate a CSRF token
$csrf_token= bin2hex(random_bytes(32));
$csrf_token_hashed= hash("sha256", $csrf_token);
$issued_at = date("Y-m-d H:i:s");
$expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

//Store the token in the database
$admin_id = ($user_role === 3) ? $user_id : null;
$staff_id = ($user_role === 2) ? $user_id : null;

$stmt = $conn->prepare("INSERT INTO csrf (TOKEN, ISSUED_AT, EXPIRES_AT, ADMIN_ID, STAFF_ID) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssii", $csrf_token_hashed, $issued_at, $expires_at, $admin_id, $staff_id);

if (!$stmt->execute()){
    die("Error inserting CSRF token: " . $stmt->error);
}
$stmt->close();

$_SESSION['csrf_token'] = $csrf_token;
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $submitted_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$submitted_token || hash("sha256", $submitted_token)!== $csrf_token_hashed){
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        exit("Invalid CSRF token.");
    }
}

$student = null;
if (isset($_GET['id'] )&& is_numeric($_GET['id'])){
    $student_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, name, phonenumber, email, course_id, faculty,department_id,class FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
     if ($result->num_rows >0){
        $student = $result->fetch_assoc();
    
    }else{
        echo "<p> No student records found.</p>";
    }
    $stmt ->close();
}
if($_SERVER["REQUEST_METHOD"]== 'POST' && isset($_POST['update'])){
    $name = $_POST['name'];
    $phonenumber=$_POST['phonenumber'];
    $email = $_POST['email'];
    $course_id = $_POST['course_id'];
    $faculty = $_POST['faculty'];
    $department_id = $_POST['department_id'];
    $class = $_POST['class'];

    $stmt = $conn -> prepare("UPDATE students SET name=? , phonenumber=?, email=?, faculty=? WHERE id=?");
    $stmt->bind_param("ssssi", $_POST['name'], $_POST['phonenumber'], $_POST['email'], $_POST['faculty'],$_POST['id']);
    if ($stmt->execute()){
        $_SESSION['message'] = "Student details updated successfully.";
    }else{
        $_SESSION['message']= "Update failed or no changes made:" . htmlspecialchars($stmt->error);
    }
    
    $stmt->close();
    header("Location: update_student.php?id=$student_id");
    exit;
}
$conn->close();
?>

<?php if (isset($_SESSION['message'])) : ?>
    <p><?= $_SESSION['message']; ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<?php if (isset($student)) : ?>
    <h1>Update Student Record</h1>
    <form method="POST">
        <input type='hidden' name='id' value='<?= htmlspecialchars($student['id']) ?>'>
        <label>Name:</label><input type='text' name='name' value='<?= htmlspecialchars($student['name']) ?>'><br>
        <label>Phone Number:</label><input type='text' name='phonenumber' value='<?= htmlspecialchars($student['phonenumber']) ?>'><br>
        <label>Email:</label><input type='text' name='email' value='<?= htmlspecialchars($student['email']) ?>'><br>
        <label>Faculty:</label><input type='text' name='faculty' value='<?= htmlspecialchars($student['faculty']) ?>'><br>
        <input type='submit' name='update' value='Update Details'>
        <a href = "student_records.php">Back</a>
        <input type = "hidden" name ="token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']);?>">
    </form>
<?php endif; ?>