<?php

session_start();
require "functions.php";
checkSessionTimeout();
is_logged_in([1]);

?>
<!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content="width=device-width, initial-scale=1.0">
        <title> Student Dashboard</title>
 <style>
    body{
        margin:0 ;
        font-family: sans-serif;
    }
    .sidepanel{
        height: 250px;
        width:0 ;
        position: fixed;
        z-index: 1;
        top: 0;
        left: 0;
        background-color: #141619;
        overflow-x : hidden;
        padding=top: 60pc;
        transition: 0.5s;
    }
    .sidepanel a{
        padding: 8px 8px 8px 32px;
        text-decoration: none;
        font-size: 25px;
        color: #b3b4bd;
        display: block;
        transition: 0.3s;

    }

    .sidepanel a:hover{
        color:#2c2e3a; 
    }
    </style>
    <body>
        <div id="sidePanel" class=".sidepanel">
            <button class="close-btn" onclick="toggleSidePanel()">x</button>
            <a href="student_record.php">Your own record.</a>
</div>
<div id="maincontent" class="main-content">
    <button class="open-btn" onclick="toggleSidePanel()">☰ Open Panel</button>
    <h1> Welcome</h1>
    <p>This is the main content area. Click the button to toggle the side panel.</p>
    </div>
<a href="logout.php"><button>Logout<button></a>
</body>
