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

require 'functions.php';
is_logged_in([3,2]);

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

        foreach ($class_info as $class_info): ?>
            <tr>
                <td><?php echo htmlspecialchars($class_info['id']); ?></td>
                <td><?php echo htmlspecialchars($class_info['classname']); ?></td>
                <td><?php echo htmlspecialchars($class_info['mode']); ?></td>
                <td><?php echo htmlspecialchars($class_info['depname']); ?></td>
                <td><a href="editclass.php?id=<?php echo $class_info['id']; ?>"> edit </a> </td> 
            </tr>
        <?php endforeach; ?>
    </tbody>

<?php if ($_SESSION['role'] == 3){      //redirect back to dashboard of role
    $redirect = "admin_dashboard.php";
}elseif($_SESSION['role'] == 2) 
    $redirect = "faculty_dashboard.php";
?>

<a href="<?php echo $redirect; ?>">back</a> <br>
<a href="newclass.php">add classes</a> <br>


