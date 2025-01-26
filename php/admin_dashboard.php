<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
} elseif ($_SESSION['role'] != 3) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            margin: 0;
            background-color: rgb(148, 214, 201);
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: rgb(51, 51, 51);
            position: relative;
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
            background-color: rgb(214, 204, 188);
        }

        .active {
            background-color: rgb(255, 255, 255);
        }

        /* Style for iframe */
        iframe {
            width: 100%;
            height: calc(100vh - 50px);
            border: none;
            display: block;
        }
    </style>
</head>
<body>
    <ul>
        <li><a href="viewclass.php" target="contentFrame">View classes</a></li>
        <li><a href="register.php" target="contentFrame">Create new student profiles</a></li>
        <li><a href="viewgradetry.php" target="contentFrame">Grades Dashboard</a></li>
        <li><a href="view_course.php" target="contentFrame">Courses Dashboard</a></li>
        <li><a href="student_records.php" target="contentFrame">View existing students</a></li>
        <li style="float:right"><a href="logout.php">Logout</a></li>
    </ul>

    <!-- iframe to load the content -->
    <iframe name="contentFrame"></iframe>
</body>
</html>
