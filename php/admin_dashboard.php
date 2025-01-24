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
<!-- 
<a href="viewclass.php">View classes</a>
<br>
<a href="register.php">Create new student profiles</a>
<br>
<a href="creategrade.php">Enter Student's Grade</a>
<br>
<a href="viewgradetry.php">View student's grade</a>
<br>
<a href="view_course.php">Courses Dashboard</a>
<br>
<a href="logout.php">Logout</a> -->
<html>
<head>
<style>
body {margin:0;}

ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #333;
  position: fixed;
  top: 0;
  width: 100%;
}

li {
  float: left;
}

li a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

li a:hover:not(.active) {
  background-color: #111;
}

.active {
  background-color:rgb(255, 255, 255);
}
</style>
</head>
<body>
<ul>
  <li><a href="viewclass.php">View classes</a></li>
  <li><a href="register.php">Create new student profiles</a></li>
  <li><a href="creategrade.php">Enter Student's Grade</a></li>
  <li><a href="viewgradetry.php">View student's grade</a></li>
  <li><a href="view_course.php">Courses Dashboard</a></li>
  <li style="float:right"><a class="active" href="logout.php">Logout</a></li>
</ul>

<div style="padding:20px;margin-top:30px;background-color:#1abc9c;height:1500px;">

</body>
</html>