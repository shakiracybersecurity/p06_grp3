<?php
session_start();
require 'functions.php'; // Ensure your database connection file is included

// Prevent students from deleting
if ($_SESSION['role'] == 'student') {
    die("Access denied: Students cannot delete records.");
}

// Only process DELETE if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $conn = db_connect(); // Ensure this function is available in functions.php

    $record_id = $_POST['record_id']; // Get record ID from form input

    // Execute DELETE query
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $record_id);

    if ($stmt->execute()) {
        echo "Record deleted successfully!";
    } else {
        echo "Error deleting record!";
    }

    $stmt->close();
    $conn->close();
}
?>
