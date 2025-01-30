<?php
// Database connection
require 'functions.php';
$conn = db_connect();
// Start session and check role
session_start();

checkSessionTimeout();

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit();
}

// Fetch all courses + department
$sql = "SELECT course.ID, course.NAME, course.CODE, course.START_DATE, course.END_DATE, 
               course.STATUS, department.NAME AS DEPARTMENT_NAME 
        FROM course 
        LEFT JOIN department ON course.DEPARTMENT_NAME = department.NAME";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
    <style>
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
        width: 80%; /* Adjust width as needed */
        max-width: 1000px; /* Optional: limit table width */
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: 1px solid;
      
        }
        th, td{
        padding: 15px;
        text-align: center;
        border: 1px solid;
        
    }
    th{
        background-color: #0a21c0;
         color: white;
    }
    h2{
    text-align: center;
    color: #2c2e3a;
    margin-top: 30px;
    margin-bottom: 20px;
    }
    button {
        background: #fff;
        color: black;
        padding: 10px;
        border: 1px solid #2c2e3a;
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
<body>
<a href="admin_dashboard.php"><button>Back</button></a>
    <h2>Courses Available</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Code</th>
            <th>Department</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['ID'] ?></td>
                <td><?= htmlspecialchars($row['NAME']) ?></td>
                <td><?= htmlspecialchars($row['CODE']) ?></td>
                <td><?= isset($row['DEPARTMENT_NAME']) ? htmlspecialchars($row['DEPARTMENT_NAME']) : 'N/A' ?></td>
                <td>
                    <?= ($row['START_DATE'] !== '0000-00-00') 
                        ? date("d-m-Y", strtotime($row['START_DATE'])) 
                        : 'N/A'; ?>
                </td>
                <td>
                    <?= ($row['END_DATE'] !== '0000-00-00') 
                        ? date("d-m-Y", strtotime($row['END_DATE'])) 
                        : 'N/A'; ?>
                </td>
            
                <td>
                    <?= htmlspecialchars($row['STATUS']) ?></td>
                <td>
                    <a href="update1.php?id=<?= $row['ID'] ?>"><button>Edit</button></a>
                    <?php if ($_SESSION['role'] == 3): // Only Admin can delete ?>
                        <a href="delete_course.php?id=<?= $row['ID'] ?>" onclick="return confirm('Are you sure?')"><button>Delete</button></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table><br>
    <!-- Create Course Button -->
    <a href="create_course.php"><button>Create Course</button></a>    
    <br></br>

</body>
</html>
<?php $conn->close(); ?>