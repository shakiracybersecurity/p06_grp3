<?php 
require "functions.php";

$conn=db_connect();
session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

checkSessionTimeout();
is_logged_in([3,2]);

$class_id = $_GET['id'];

$stmt = $conn->prepare("SELECT class.id as class_id , class.name as classname, class.mode, department.name as depname,
                        department.id as dep_id, faculty.name as teacher, faculty.id as teacher_id, modules.name as modulename, modules.id as module_id
                        FROM class 
                        LEFT JOIN department ON class.department_id=department.id 
                        LEFT JOIN faculty ON class.teacher_id=faculty.id
                        LEFT JOIN modules on class.modules_id=modules.id WHERE class.id = ?
                         "); //select to be displayed in the form the already selected options for the class
$stmt->bind_param("i", $class_id);
$stmt -> execute(); 
$result = $stmt->get_result();
$class_info = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING); //csrf token
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
    $stmt = $conn->prepare("UPDATE class SET name = ?, mode = ?, department_id = ?, teacher_id = ?, modules_id = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssiiii", $classname, $mode, $dep, $teacher, $module, $class_id);
        
        if ($stmt->execute()) {
            header("Location: viewclass.php"); //goes back to view class if update is successful
        } else {
            echo "Error during registration.";
        }
        $stmt->close();
    } else {
        echo "Failed to prepare the statement.";
    }
}



?>
<!-- css -->
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
 
    /* Input and Select Styles */
    input[type="text"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        box-sizing: border-box;
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
    a {
        text-decoration: none;
    }
    </style>

<a href="viewclass.php"><button>Back</button></a> <br>

<form method="POST">
    <h2> Edit class </h2>
    Class name: <input type="text" name="class" value = "<?php echo $class_info['classname'];?>" required ><br>

    Class mode:
    <input type = "radio" name= "mode" id ="semester" value= "semester" <?php if($class_info['mode'] == "semester"){echo "checked";}?>/>
    <label for = "semester">Semester</label>
    <input type = "radio" name= "mode" id ="term" value= "term" <?php if($class_info['mode'] == "term"){echo "checked";}?>/>
    <label for = "term">Term</label>
    <br>

    Department:  
    <select name="department" id="department">
        <?php 
            $stmt = $conn->prepare("select id, name from department");
            $stmt->execute();
            $result = $stmt->get_result();
            $department = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            foreach ($department as $department): ?>
            <option value= "<?php echo $department['id'];?>" 
            <?php if ($class_info['dep_id']== $department['id']){echo "selected";}?>> 
                <?php echo $department['name'] ?> </option>
            <?php endforeach; ?>
            </select><br>

    Teacher:  
    <select name="teacher" id="teacher">
        <?php 
            $stmt = $conn->prepare("select id, name from faculty");
            $stmt->execute();
            $result = $stmt->get_result();
            $teacher = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            foreach ($teacher as $teacher): ?>
            <option value= "<?php echo $teacher['id'];?>" 
            <?php if ($class_info['teacher_id']== $teacher['id']){echo "selected";}?>> 
                <?php echo $teacher['name'] ?> </option>
            <?php endforeach; ?>
            </select><br>

    Module:  
    <select name="module" id="module">
        <?php 
            $stmt = $conn->prepare("select id, name from modules");
            $stmt->execute();
            $result = $stmt->get_result();
            $module = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            foreach ($module as $module): ?>
            <option value= "<?php echo $module['id'];?>" 
            <?php if ($class_info['module_id']== $module['id']){echo "selected";}?>> 
                <?php echo $module['name'] ?> </option>
            <?php endforeach; ?>
            </select>
            
<br>   
<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">    
<input type="submit" value="update">

</form>



