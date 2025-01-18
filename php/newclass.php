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

<table>
    <thead>
        <tr>
            <th>departmet</th>
            <th>module</th>
            <th>mode</th>
            <th>class</th>
        </tr>
    </thead>  


<a href="admin_dashboard.php">back</a> <br>
<form method="POST">
    class name: <input type="text" name="class" required><br>

    class mode:
    <input type = "radio" name= "mode" id ="semester"value= "semester"/>
    <label for = "semester">Semester</label>
    <input type = "radio" name= "mode" id ="term"value= "term"/>
    <label for = "term">Term</label>
    <br>

    department:  
    <select name="course" id="course">
        <?php 
            $stmt = $conn->prepare("select id, name from department");
            $stmt->execute();
            $result = $stmt->get_result();
            $department = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            foreach ($department as $department): ?>
            <option value= <?php $department['id']; ?>> <?php echo $department['name']; ?> </option>
            <?php endforeach; ?>

                
        ?><!--
    <option value="1">1</option> 
    <option value="2">2</option>
    <option value="3">3</option>
-->
<input type="submit" value="add">

</form>