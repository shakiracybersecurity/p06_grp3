<?php

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}elseif($_SESSION['role'] != 3){
    header("Location: login.php");
    exit();
}
echo $_SESSION['username'];
//session_destroy()
?>

<p> test admin</p>  

<a href="viewclass.php">View classes</a>
<br>
<a href="register.php">Create new student profiles</a>
<br>
<a href="creategrade.php">Enter Student's Grade</a>
<br>
<a href="viewgrade.php">View student's grade</a>
<br>
<<<<<<< HEAD
<a href="student_records.php"> View existing students</a>
=======
<a href="create_course.php">Courses Dashboard</a>
>>>>>>> 51817e137c845ddfe7d32af6af038bf2504808d6
<br>
<a href="logout.php">Logout</a>

