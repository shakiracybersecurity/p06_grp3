<?php

session_start();
require "functions.php";
checkSessionTimeout();
is_logged_in([1]);

?>

<a href="student_record.php">View your own record</a>
<br>
<a href="logout.php">Logout</a>