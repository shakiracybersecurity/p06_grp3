<?php 
require "functions.php";

db_connect();
session_start();
is_logged_in([3,2]);


echo $class_info['name'];



?>