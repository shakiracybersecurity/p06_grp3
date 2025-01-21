<?php
// check if the user is logged in and role of user
function is_logged_in($allowed) {
    if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}elseif(!in_array($_SESSION['role'], $allowed)){
    header("Location: login.php");
    exit();
}}

?>
