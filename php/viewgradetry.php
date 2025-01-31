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

// Initialize filters
$search = $_GET['search'] ?? '';
$filter_grade = $_GET['filter_grade'] ?? '';
$filter_course = $_GET['filter_course'] ?? '';

// Base query with placeholders
$query = "SELECT g.ID, SCORE, s.NAME AS student_name, c.NAME AS course_name, GRADE 
          FROM grades g
          LEFT JOIN students s ON g.STUDENT_ID = s.ID
          LEFT JOIN course c ON g.COURSE_ID = c.ID
          WHERE 1=1";

// Parameters for prepared statements
$params = [];
$types = '';

// Add filters to the query
if (!empty($search)) {
    $query .= " AND (s.NAME LIKE ? OR s.ID LIKE ? OR c.NAME LIKE ?)";
    $search_param = "%$search%";
    $params[] = &$search_param;
    $params[] = &$search_param;
    $params[] = &$search_param;
    $types .= "sss"; // Three strings
}

// Decode the grade filter to handle special characters like +
$filter_grade = urldecode($_GET['filter_grade'] ?? '');

// Query with exact matching and trimmed grades
if (!empty($filter_grade)) {
    $query .= " AND TRIM(GRADE) = ?";
    $params[] = &$filter_grade;
    $types .= "s";
}

// Debugging for filters
error_log("Filter Grade: " . $filter_grade);
error_log("Query: $query | Params: " . json_encode($params));



if (!empty($filter_course)) {
    $query .= " AND c.ID = ?";
    $params[] = &$filter_course;
    $types .= "i"; // One integer
}

// Prepare the statement
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters if there are any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Execute and fetch results
$stmt->execute();
$result = $stmt->get_result();
$grades = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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
    /* Style for the table container */
    *{
    margin: 0;
    box-sizing: border-box;
    font-family: sans-serif;
    }
    body{
    margin-left: auto;
    margin-right: auto;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color:#b3b4bd; 
    background-size: cover;
    }
    table {
        margin-left: auto;
        margin-right: auto;
        border-collapse: collapse;
        width: 95%; /* Adjust width as needed */
        max-width: 1000px; /* Optional: limit table width */
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: 1px solid;
      
        }

    /* Header styles */
    th, td{
        padding: 15px;
        text-align: center;
        border: 1px solid;
        
    }
    th{
        background-color: #0a21c0;
         color: white;
    }

    /* Alternating row colors */
    tbody tr:nth-child(odd) {
        background-color: #f2f2f2;
    }

    tbody tr:nth-child(even) {
        background-color: #ffffff;
    }

    /* Total Students count */
    p {
        font-weight: bold;
        text-align: right;
        margin-right: 10px;
        color: #050a44;
    }

    /* Filters */
    select{
    width: 200px; /* Adjust width */
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    color: #333;
    margin-bottom: 20px;
    }
    select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
    input[type="text"]{
    padding: 10px;
    margin-top: 8px;
    border: none;
    border-radius: 15px;
    background: white;
    border: 1px solid #2c2e3a;
    color: #141619;
    font-size: 15px;
    margin-bottom: 20px;
}
    a {
        text-decoration: none;
        color: #2c2e3a;
        font-weight: bold;
    }

    a:hover {
        color: #141619;
    }
    button {
        background: #fff;
        padding: 10px;
        border-radius: 10px;
        cursor: pointer;
        margin-top: 15px;
        border: none;
}
    button:hover {
        margin-top: 15px;
        background: #3b3ec0;
        color: white;
        outline: 1px solid #fff;
}
</style>

<a href="<?php echo $redirect; ?>"><button>Back </button></a> <br>
<br>

<!-- Search and Filter Links -->
<div style="margin-bottom: 20px;">
    Search: <input type="text" id="search" placeholder="Search by student name, ID, or course name" 
    style="width: 300px;" oninput="updateSearch(this.value)" 
    value="<?php echo htmlspecialchars($search); ?>">


    <br>Filter by Grade:
    <select id="filter_grade" onchange="applyFilter()">
        <option value="">All Grades</option>
        <option value="A" <?php echo $filter_grade === 'A' ? 'selected' : ''; ?>>A</option>
        <option value="B+" <?php echo $filter_grade === 'B+' ? 'selected' : ''; ?>>B+</option>
        <option value="B" <?php echo $filter_grade === 'B' ? 'selected' : ''; ?>>B</option>
        <option value="C+" <?php echo $filter_grade === 'C+' ? 'selected' : ''; ?>>C+</option>
        <option value="C" <?php echo $filter_grade === 'C' ? 'selected' : ''; ?>>C</option>
        <option value="D+" <?php echo $filter_grade === 'D+' ? 'selected' : ''; ?>>D+</option>
        <option value="D" <?php echo $filter_grade === 'D' ? 'selected' : ''; ?>>D</option>
        <option value="F" <?php echo $filter_grade === 'F' ? 'selected' : ''; ?>>F</option>
    </select>

    <br>Filter by Course:
    <select id="filter_course" onchange="applyFilter()">
        <option value="">All Courses</option>
        
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['ID']; ?>" <?php echo $filter_course == $course['ID'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($course['NAME']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <a href="?"><button>Reset Filters</button></a>
</div>

<!-- Table with CSS Styling -->
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
    <tbody>
        <?php 
        $count = 0;
        foreach ($grades as $grade): ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo htmlspecialchars($grade['student_name']); ?></td>
                <td><?php echo htmlspecialchars($grade['course_name']); ?></td>
                <td><?php echo htmlspecialchars($grade['SCORE']); ?></td>
                <td><?php echo htmlspecialchars($grade['GRADE']); ?></td>
                <td>
                    <a href="editgrade.php?id=<?php echo $grade['ID']; ?>"><button>Edit</button></a> 
                    <?php if (can_delete()): ?>
                        <a href="deletegrade.php?id=<?php echo $grade['ID']; ?>"><button>Delete</button></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button onclick="location.href='creategrade.php'" >
    Enter New Student Grade
</button>
<p>Total Students: <?php echo $count; ?></p>

<script>
function applyFilter() {
    const grade = document.getElementById('filter_grade').value;
    const course = document.getElementById('filter_course').value;
    const search = document.getElementById('search').value;
    const query = `?filter_grade=${grade}&filter_course=${course}&search=${search}`;
    window.location.href = query;
}

function updateSearch(value) {
    const grade = document.getElementById('filter_grade').value;
    const course = document.getElementById('filter_course').value;
    const query = `?filter_grade=${grade}&filter_course=${course}&search=${value}`;
    window.location.href = query;
}
</script>






