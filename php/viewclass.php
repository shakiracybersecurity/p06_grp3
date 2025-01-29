<?php
require 'functions.php';
$conn = db_connect();
session_start();


checkSessionTimeout();
is_logged_in([3,2]);


if ($_SESSION['role'] == 3){      //redirect back to dashboard of role
    $redirect = "admin_dashboard.php";
}elseif($_SESSION['role'] == 2) 
    $redirect = "faculty_dashboard.php";

$stmt = $conn->prepare("SELECT class.id, class.name as classname, class.mode, department.name as depname
                        FROM class LEFT JOIN department ON class.department_id=department.id
                        UNION
                        SELECT class.id, class.name, class.mode, department.name 
                        FROM department RIGHT JOIN class ON class.department_id=department.id");
$stmt -> execute();
$result = $stmt->get_result();
$class_info = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<a href="<?php echo $redirect; ?>">back</a> <br>
<br>
<table>
    <thead>
        <tr>
            <th>id</th>
            <th>name</th>
            <th>mode</th>
            <th>department</th>
        </tr>
    </thead>  
    <tbody>
        <?php 
        $count = 0;
        foreach ($class_info as $class_info): ?>
            <tr>
                <td><?php echo ($count += 1) ?></td>
                <td><?php echo htmlspecialchars($class_info['classname']); ?></td>
                <td><?php echo htmlspecialchars($class_info['mode']); ?></td>
                <td><?php echo htmlspecialchars($class_info['depname']); ?></td>
                <td><a href="editclass.php?id=<?php echo $class_info['id']; ?>"> edit </a> </td> 
                <?php 
                if(can_delete()): ?>
                <td><a href="deleteclass.php?id=<?php echo $class_info['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
                <?php endif ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<br><a href="newclass.php">add classes</a> <br>


