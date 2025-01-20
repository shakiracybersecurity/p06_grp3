<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}elseif($_SESSION['role'] != 1){
    header("Location: login.php");
    exit();
}
echo $_SESSION['username'];
//session_destroy()

?>

<p> test student </p>

<a href="student_record.php">View your own record</a>
<br>
<a href="logout.php">Logout</a>