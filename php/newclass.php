<?php

require 'functions.php';
$conn = db_connect();
session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

//check session timeout and users role
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
    $teacher = $_POST['teacher'];
    $module = $_POST['module'];

    // Secure: Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO class (name, mode, department_id, teacher_id, modules_id) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssiii", $classname, $mode, $dep, $teacher, $module);
        
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
<html>
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
h2{
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
input[type="text"]{
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
    margin-top: 30px;
    margin-bottom: 30px;
    font-size: 15px;
    color: #2c2e3a;
}
input[type="radio"]{
    padding: 10px;
    border: none;
    border-radius: 10px;
    background: transparent;
    border: 1px solid #2c2e3a;
    color: #141619;
    font-size: 13px;
    margin-bottom: 20px;
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
}
input[type="submit"]{
    background: #fff;
    color: black;
    padding: 10px;
    border: 1px solid #2c2e3a;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 15px;
}
button:hover,input[type="submit"]:hover {
    margin-top: 15px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}
a {
    text-decoration: none;
    }
</style>
<a href="viewclass.php"><button>Back</button></a><br>
<form method="POST">

    <div class="container">
    <h2> Create new class </h2>
    Class name: <input type="text" name="class" required placeholder="Class Name"><br>

    Class mode:
    <input type = "radio" name= "mode" id ="semester" value= "semester"/>
    <label for = "semester">Semester</label>
    <input type = "radio" name= "mode" id ="term" value= "term"/>
    <label for = "term">Term</label>
    <br>

    Department:  
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
    
    Teacher:
    <select name="teacher" id="teacher" required>
        <option value = "" disabled selected hidden> please choose </option>
        <?php 
            $stmt = $conn->prepare("select id, name from faculty");
            $stmt->execute();
            $result = $stmt->get_result();
            $teacher = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            foreach ($teacher as $teacher): ?>
            <option value= "<?php echo $teacher['id']; ?>"> <?php echo $teacher['name'] ?> </option>
            <?php endforeach; ?>
    </select>

    <br>
    Module:
    <select name="module" id="module" required>
        <option value = "" disabled selected hidden> please choose </option>
        <?php 
            $stmt = $conn->prepare("select id, name from modules");
            $stmt->execute();
            $result = $stmt->get_result();
            $module = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            foreach ($module as $module): ?>
            <option value= "<?php echo $module['id']; ?>"> <?php echo $module['name'] ?> </option>
            <?php endforeach; ?>
    </select>
   
<br>
<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">                
<input type="submit" value="add">
</div>
</form>