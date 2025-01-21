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

echo $class_info['id'], $class_info['classname'];


?>