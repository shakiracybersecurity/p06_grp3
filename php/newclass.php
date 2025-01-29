<?php

require 'functions.php';
$conn = db_connect();
session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}


is_logged_in([3,2]);
checkSessionTimeout();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }

    // Secure: Sanitize user inputs
    $mode = $_POST['mode'];
    $classname = htmlspecialchars(trim($_POST['class']));
    $dep = $_POST['department'];

    // Secure: Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO class (name, mode, department_id) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssi", $classname, $mode, $dep);
        
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



<a href="viewclass.php">back</a> <br>
<form method="POST">
    class name: <input type="text" name="class" required><br>

    class mode:
    <input type = "radio" name= "mode" id ="semester" value= "semester"/>
    <label for = "semester">Semester</label>
    <input type = "radio" name= "mode" id ="term" value= "term"/>
    <label for = "term">Term</label>
    <br>

    department:  
    <select name="department" id="department" required>
        <option value = "" disabled selected hidden> please choose </option>
        <?php 
            $stmt = $conn->prepare("select id, name from department");
            $stmt->execute();
            $result = $stmt->get_result();
            $department = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            foreach ($department as $department): ?>
            <option value= "<?php echo $department['id']; ?>"> <?php echo $department['name'] ?> </option>
            <?php endforeach; ?>
    </select>
   
<br>
<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">                
<input type="submit" value="add">

</form>