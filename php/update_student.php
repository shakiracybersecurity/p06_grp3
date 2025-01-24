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
    </form>
<?php endif; ?>