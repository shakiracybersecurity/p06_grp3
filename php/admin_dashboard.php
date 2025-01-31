<?php
session_start();
require "functions.php";
checkSessionTimeout();
is_logged_in([3]);
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            background-color:#b3b4bd;
        }

        /* Side Panel Styling */
        .sidepanel {
            height: 100%; /* Full-height */
            width: 0; /* Initial width is 0 */
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #141619; /* Dark background */
            overflow-x: hidden; /* Disable horizontal scroll */
            transition: 0.5s; /* Smooth transition */
            padding-top: 60px; /* Space from top */
        }

        .sidepanel a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #b3b4bd;
            display: block;
            transition: 0.3s;
        }

        .sidepanel a:hover {
            color: #2c2e3a;
        }

        .sidepanel .closebtn {
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 36px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }

        /* Open Button Styling */
        .openbtn {
            font-size: 20px;
            cursor: pointer;
            background-color: #0a21c0;
            color: white;
            padding: 10px 15px;
            border: none;
            transition: 0.3s;
        }

        .openbtn:hover {
            background-color: #050a44;
        }

        /* Main Content */
        .maincontent {
            margin-left: 0; /* Start with no margin */
            transition: margin-left 0.5s; /* Smooth transition */
            padding: 20px;
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
  <!-- Side Panel -->
  <div id="sidePanel" class="sidepanel">
        <button class="closebtn" onclick="toggleSidePanel()">×</button>
        <a href="viewclass.php">View classes</a>
        <a href="assignments.php?action=create">Create new student profiles</a>
        <a href="assignment.php?action=create">Create new student profiles</a>
        <a href="viewgradetry.php">Grade details</a>
        <a href="view_course.php">Courses Dashboard</a>
        <a href="assignments.php?action=read"> View existing students</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div id="maincontent" class="maincontent">
        <button class="openbtn" onclick="toggleSidePanel()">☰ Open Panel</button>
        <h1>Welcome</h1>
        <p>This is the main content area. Click the button to toggle the side panel.</p>
    </div>
    
    <iframe name="contentFrame"></iframe>
    <script>
        function toggleSidePanel() {
            const sidePanel = document.getElementById("sidePanel");
            const mainContent = document.getElementById("maincontent");

            if (sidePanel.style.width === "250px") {
                sidePanel.style.width = "0";
                mainContent.style.marginLeft = "0";
            } else {
                sidePanel.style.width = "250px";
                mainContent.style.marginLeft = "250px";
            }
        }
    </script>
<body>
</html>
