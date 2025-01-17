<?php

$host = 'localhost';
$dbname = 'robotic course management'; // Updated for clarity
$user = 'root';
$pass = '';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Secure: Sanitize user inputs
    $mode = $_POST['mode'];
    $classname = htmlspecialchars(trim($_POST['class']));

    // Secure: Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO class (name, mode) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $classname, $mode);
        
        if ($stmt->execute()) {
            echo "class added!";
        } else {
            echo "Error during registration.";
        }
        $stmt->close();
    } else {
        echo "Failed to prepare the statement.";
    }

}
?>


<form method="POST">
    class name: <input type="text" name="class" required><br>

    <input type = "radio" name= "mode" id ="semester"value= "semester"/>
    <label for = "semester">Semester</label>
    <input type = "radio" name= "mode" id ="term"value= "term"/>
    <label for = "term">Term</label>
    <br>
    <!-- not done 
    <select name="course" id="course">
    <option value="1">1</option> 
    <option value="2">2</option>
    <option value="3">3</option>
-->
<input type="submit" value="add">

</form>