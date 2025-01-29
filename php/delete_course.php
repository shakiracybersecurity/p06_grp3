<?php
// Database connection
require 'functions.php';
$conn = db_connect();
// Start session and check role
session_start();

checkSessionTimeout();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 3) { // Only Admin can delete
    header("Location: login.php");
    exit();
}

// Delete course
$id = $_GET['id'];
$sql = "DELETE FROM course WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: view_course.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
