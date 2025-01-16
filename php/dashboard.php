<?php
// Start the session
session_start();

// Check if the user is logged in

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
    

// Role-based access control
if ($_SESSION['role'] === '1') {
    header("Location: student_dashboard.php");
    echo "Welcome, ", $_SESSION['username'] ," You have full access.<br>";
} elseif ($_SESSION['role'] === '2') {
    header("Location: faculty_dashboard.php");
} elseif ($_SESSION['role'] === '3') {    
    header("Location: admin_dashboard.php");
    echo "Welcome, " . htmlspecialchars($_SESSION['username']) . ". You are logged in as a user.<br>";
    echo $_SESSION['username'];
}
?>

<!-- General dashboard content -->
<a href="logout.php">Logout</a>
