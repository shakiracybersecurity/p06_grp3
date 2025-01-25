<?php
function db_connect(){
    $host = 'localhost';
    $dbname = 'robotic course management'; // Updated for clarity
    $user = 'root';
    $pass = '';

    // Connect to the database
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// check if the user is logged in and role of user
function is_logged_in($allowed) {
    if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}elseif(!in_array($_SESSION['role'], $allowed)){
    header("Location: login.php");
    exit();
}}

function can_delete(){
    if ($_SESSION['role'] == 3){
        return TRUE;
    }else{
        return FALSE;
    }
}

function checkSessionTimeout($timeout_duration = 30) {
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        if ($inactive_time > $timeout_duration) {
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}
?>
