<?php
// Start the session
session_start();

// Check if the user is logged in

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
    

// Role-based access control: only admins can access specific parts
if ($_SESSION['role'] === '1') {
    echo "Welcome, ", $_SESSION['username'] ," You have full access.<br>";
} else {
    echo "Welcome, " . htmlspecialchars($_SESSION['username']) . ". You are logged in as a user.<br>";
    echo $_SESSION['username'];
}
?>

<!-- General dashboard content -->
<a href="logout.php">Logout</a>
