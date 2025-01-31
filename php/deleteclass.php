<?php
require "functions.php";

$conn = db_connect();
session_start();

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

is_logged_in([3]);
checkSessionTimeout();

$id = $_GET['id'];


$stmt = $conn->prepare("DELETE FROM class WHERE id = ?");
$stmt -> bind_param("i", $id);
$stmt -> execute();
$stmt -> close();
header("Location: viewclass.php");
    
?>
