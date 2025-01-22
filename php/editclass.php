<?php 
require "functions.php";

$conn=db_connect();
session_start();
is_logged_in([3,2]);


$class_id = $_GET['id'];

$stmt = $conn->prepare("SELECT class.id, class.name as classname, class.mode, department.name as depname
                        FROM class LEFT JOIN department ON class.department_id=department.id WHERE class.id = ?
                        UNION
                        SELECT class.id, class.name, class.mode, department.name 
                        FROM department RIGHT JOIN class ON class.department_id=department.id 
                         ");
$stmt->bind_param("i", $class_id);
$stmt -> execute();
$result = $stmt->get_result();
$class_info = $result->fetch_assoc();
$stmt->close();




?>

<form method="POST">
    class name: <input type="text" name="class" required><br>

    class mode:
    <input type = "radio" name= "mode" id ="semester" value= "semester"/>
    <label for = "semester">Semester</label>
    <input type = "radio" name= "mode" id ="term" value= "term"/>
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
            <option value= "<?php echo $department['id']; ?>"> <?php echo $department['name'] ?> </option>
            <?php endforeach; ?>

                
<input type="submit" value="add">

</form>

<a href="viewclass.php">back</a> <br>

