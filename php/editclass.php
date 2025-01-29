<?php 
require "functions.php";

$conn=db_connect();
session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

checkSessionTimeout();
is_logged_in([3,2]);

try {
    $class_id = $_GET['id'];

} catch (Exception $e) {
    header("Location: viewgrade.php");
}
$stmt = $conn->prepare("SELECT class.id as class_id , class.name as classname, class.mode, department.name as depname, department.id as dep_id
                        FROM class LEFT JOIN department ON class.department_id=department.id WHERE class.id = ?
                        UNION
                        SELECT class.id as class_id , class.name as classname, class.mode, department.name as depname, department.id as dep_id
                        FROM department RIGHT JOIN class ON class.department_id=department.id 
                         ");
$stmt->bind_param("i", $class_id);
$stmt -> execute();
$result = $stmt->get_result();
$class_info = $result->fetch_assoc();
$stmt->close();

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
    $stmt = $conn->prepare("UPDATE class SET name = ?, mode = ?, department_id = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ssii", $classname, $mode, $dep, $class_id);
        
        if ($stmt->execute()) {
            header("Location: viewclass.php");
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
    class name: <input type="text" name="class" value = "<?php echo $class_info['classname'];?>" required ><br>

    class mode:
    <input type = "radio" name= "mode" id ="semester" value= "semester" <?php if($class_info['mode'] == "semester"){echo "checked";}?>/>
    <label for = "semester">Semester</label>
    <input type = "radio" name= "mode" id ="term" value= "term" <?php if($class_info['mode'] == "term"){echo "checked";}?>/>
    <label for = "term">Term</label>
    <br>

    department:  
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
            </select>
            
<br>   
<input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">    
<input type="submit" value="update">

</form>



