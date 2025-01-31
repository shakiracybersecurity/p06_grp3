<?php
require 'functions.php';
$conn = db_connect();
session_start();

//check session timeout and users role
checkSessionTimeout();
is_logged_in([3,2]);

if ($_SESSION['role'] == 3){      //redirect back to dashboard of role
    $redirect = "admin_dashboard.php";
}elseif($_SESSION['role'] == 2) 
    $redirect = "faculty_dashboard.php";

$stmt = $conn->prepare("SELECT class.id, class.name as classname, class.mode, department.name as depname, faculty.name as teacher, modules.name as modulename
                        FROM class 
                        LEFT JOIN department ON class.department_id=department.id
                        LEFT JOIN faculty ON class.teacher_id=faculty.id
                        LEFT JOIN modules on class.modules_id=modules.id
                        ");
$stmt -> execute();
$result = $stmt->get_result();
$class_info = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
 <!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content="width=device-width, initial-scale=1.0">
        <title> View Classes</title>
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
    </head>
    <body>
    <a href="<?php echo $redirect; ?>"><button>Back</button></a>
    <h2> Current Classes </h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>MODE</th>
            <th>DEPARTMENT</th>
            <th>MODULE</th>
            <th>TEACHER</th>
            <th>ACTIONS</th>
        </tr>
    </thead>  
    <tbody>
        <?php 
        $count = 0;
        foreach ($class_info as $class_info): ?>
        
            <tr>
                <td><?php echo ($count += 1) ?></td>
                <td><?php echo htmlspecialchars($class_info['classname']); ?></td>
                <td><?php echo htmlspecialchars($class_info['mode']); ?></td>
                <td><?php echo htmlspecialchars($class_info['depname']); ?></td>
                <td><?php echo htmlspecialchars($class_info['modulename']); ?></td>
                <td><?php echo htmlspecialchars($class_info['teacher']); ?></td>
                <td><a href="editclass.php?id=<?php echo $class_info['id']; ?>"><button>Edit</button> </a> <br>
                <?php 
                if(can_delete()): ?>
                <a href="deleteclass.php?id=<?php echo $class_info['id'] ?>" onclick="return confirm('Are you sure?')"><button>Delete</button></a></td>
                <?php endif ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
        <div class = "button">
            <a href="newclass.php"><button>Add classes</button></a>
        </div>
                </body>
                </html> 
