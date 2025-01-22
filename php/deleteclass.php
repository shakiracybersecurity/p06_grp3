<?php
require "funcions.php";

$conn = db_connect();
session_start();
is_logged_in([3]);

$id = $_GET['id'];

function delete(){
    $stmt = $conn->prepare("DELETE FROM class WHERE id = ?");
}
?>

<p> are you sure you want to delete this </p>
<button onclick = "delete()"> delete </button>
<button> no </button>