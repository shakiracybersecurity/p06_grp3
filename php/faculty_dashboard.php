<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}elseif($_SESSION['role'] != 2){
    header("Location: login.php");
    exit();
}
echo $_SESSION['username'];
//session_destroy()

?>

<p> test faculty </p>

<a href="viewclass.php">view classes</a>
<br>
<a href="register.php">Create new student profiles</a>
<br>
<a href="creategrade.php">Enter Student's Grade</a>
<br>
<a href="viewgrade.php">View student's grade</a>
<br>
<a href="student_records.php"> View existing students</a>
<br>
<a href="logout.php">Logout</a>
