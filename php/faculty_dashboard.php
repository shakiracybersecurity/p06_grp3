<?php

session_start();
require "functions.php";
checkSessionTimeout();
is_logged_in([2]);


?>
<html>
<head>
<style>
body {margin:0;}

.active {
  background-color:rgb(128, 129, 110);
}

ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: rgb(51,51,51);
  position: fixed;
  top: 0;
  width: 100%;
}




<a href="viewclass.php">view classes</a>
<br>
<a href="create1.php">Create new student profiles</a>
<br>
<a href="creategrade.php">Enter Student's Grade</a>
<br>
<a href="viewgradetry.php">View student's grade</a>
<br>    
<a href="view_course.php">Courses Dashboard</a>
<br>
<a href="read1.php"> View existing students</a>
<br>
<a href="view_assignments.php"> View Student Course Assignments</a>
<br>

<a href="logout.php">Logout</a>




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
  background-color: rgb(214, 204, 188);
}

.active {
  background-color:rgb(255, 255, 255);
}
</style>
</head>
<body>
<ul>
  <li><a href="viewclass.php">View classes</a></li>
  <li><a href="create1.php">Create new student profiles</a></li>
  <li><a href="viewgradetry.php">Grade details</a></li>
  <li><a href="view_course.php">Courses Dashboard</a></li>
  <li><a href="read1.php"> View existing students</a></li>
  <li><a href="view_assignments.php"> View Student Course Assignments</a><li>
  <li style="float:right"><a href="logout.php">Logout</a></li>
</ul>

<div style="padding:20px;margin-top:30px;background-color:rgb(148, 214, 201);height:1500px;">

</body>
</html>

