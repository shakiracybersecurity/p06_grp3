<?php
require "functions.php";

$conn = db_connect();
session_start();
is_logged_in([3]);

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delete = $_POST['delete'];
    
    if ($delete == "delete"){
        $stmt = $conn->prepare("DELETE FROM class WHERE id = ?");
        $stmt -> bind_param("i", $id);
        $stmt -> execute();
        $stmt -> close();
        echo "deleted";
        header("Location: viewclass.php");
    } else {
        header("Location: viewclass.php");
    }
    
}

?>

<form method="post"> 
    <input type="submit" name="delete"
            class="button" value="delete" /> 

    <input type="submit" name="delete"
            class="button" value="no" /> 
</form>