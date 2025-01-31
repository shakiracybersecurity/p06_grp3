<?php

// Database connection
require 'functions.php';
$conn = db_connect();

session_start();

checkSessionTimeout();
is_logged_in([3, 2]);

if ($_SESSION['role'] == 3) {
    $redirect = "admin_dashboard.php";
} elseif ($_SESSION['role'] == 2) {
    $redirect = "faculty_dashboard.php";
}

// Fetch all grades
$query = "SELECT g.ID, SCORE, s.NAME AS student_name, c.NAME AS course_name, GRADE 
          FROM grades g
          LEFT JOIN students s ON g.STUDENT_ID = s.ID
          LEFT JOIN course c ON g.COURSE_ID = c.ID";

$result = $conn->query($query);
$grades = $result->fetch_all(MYSQLI_ASSOC);

// Fetch courses for the filter dropdown
$course_result = $conn->query("SELECT ID, NAME FROM course");
$courses = $course_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content="width=device-width, initial-scale=1.0">
        <title> Robotic Management System</title>
<style>
    * {
        margin: 0;
        box-sizing: border-box;
        font-family: sans-serif;
    }
    body {
        margin-left: auto;
        margin-right: auto;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #b3b4bd;
        background-size: cover;
    }
    table {
        margin-left: auto;
        margin-right: auto;
        border-collapse: collapse;
        width: 95%;
        max-width: 1000px;
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: 1px solid;
    }
    th, td {
        padding: 15px;
        text-align: center;
        border: 1px solid;
    }
    th {
        background-color: #0a21c0;
        color: white;
    }
    tbody tr:nth-child(odd) {
        background-color: #f2f2f2;
    }
    tbody tr:nth-child(even) {
        background-color: #ffffff;
    }
    p {
        font-weight: bold;
        text-align: right;
        margin-right: 10px;
        color: #050a44;
    }
    select, input[type="text"] {
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }
    button {
        background: #fff;
        padding: 10px;
        border-radius: 10px;
        cursor: pointer;
        border: none;
    }
    button:hover {
        background: #3b3ec0;
        color: white;
    }
</style>

<a href="<?php echo $redirect; ?>"><button>Back</button></a> <br><br>

<div style="margin-bottom: 20px;">
    Search: <input type="text" id="search" placeholder="Search by student name or course name" style="width: 300px;">
    <br>Filter by Grade:
    <select id="filter_grade">
        <option value="">All Grades</option>
        <option value="A">A</option>
        <option value="B+">B+</option>
        <option value="B">B</option>
        <option value="C+">C+</option>
        <option value="C">C</option>
        <option value="D+">D+</option>
        <option value="D">D</option>
        <option value="F">F</option>
    </select>
    <br>Filter by Course:
    <select id="filter_course">
        <option value="">All Courses</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['NAME']; ?>"><?php echo htmlspecialchars($course['NAME']); ?></option>
        <?php endforeach; ?>
    </select>
    <button onclick="resetFilters()">Reset Filters</button>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Student's Name</th>
            <th>Course Name</th>
            <th>Score</th>
            <th>Grade</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<p>Total Students: <span id="total_students"></span></p>

<button onclick="location.href='creategrade.php'">Enter New Student Grade</button>

<script>
const allGrades = <?php echo json_encode($grades); ?>;

document.getElementById('filter_grade').addEventListener('change', applyFilter);
document.getElementById('filter_course').addEventListener('change', applyFilter);
document.getElementById('search').addEventListener('input', applyFilter);

document.addEventListener('DOMContentLoaded', () => {
    renderTable(allGrades);
});

function applyFilter() {
    const gradeFilter = document.getElementById('filter_grade').value;
    const courseFilter = document.getElementById('filter_course').value;
    const searchFilter = document.getElementById('search').value.toLowerCase();

    let filteredGrades = allGrades.filter(grade => {
        let match = true;
        if (gradeFilter && grade['GRADE'].trim() !== gradeFilter) match = false;
        if (courseFilter && grade['course_name'] !== courseFilter) match = false;
        if (searchFilter &&
            !grade['student_name'].toLowerCase().includes(searchFilter) &&
            !grade['course_name'].toLowerCase().includes(searchFilter)) match = false;
        return match;
    });
    renderTable(filteredGrades);
}

function renderTable(data) {
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    let count = 0;
    data.forEach(grade => {
        count++;
        tbody.innerHTML += `
            <tr>
                <td>${count}</td>
                <td>${grade['student_name']}</td>
                <td>${grade['course_name']}</td>
                <td>${grade['SCORE']}</td>
                <td>${grade['GRADE']}</td>
                <td>
                    <a href='editgrade.php?id=${grade['ID']}'><button>Edit</button></a>
                    <a href='deletegrade.php?id=${grade['ID']}'><button>Delete</button></a>
                </td>
            </tr>
        `;
    });
    document.getElementById('total_students').innerText = count;
}

function resetFilters() {
    document.getElementById('filter_grade').value = '';
    document.getElementById('filter_course').value = '';
    document.getElementById('search').value = '';
    renderTable(allGrades);
}
</script>
