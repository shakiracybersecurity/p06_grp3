
<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    
    exit();
}
echo $_SESSION['username'];
?>

<p> test admin</p>

<a href="newclass.php">Create new classes</a>
<br>
<a href="register.php">Create new student profiles</a>
<br>
<a href="logout.php">Logout</a>