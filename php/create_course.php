<?php
// Database connection
require 'functions.php';
$conn = db_connect();

// Start session and check role
session_start();

checkSessionTimeout();

// Generate and store a new CSRF token if it doesn't exist
if (empty($_SESSION['csrf_plain'])) {
    $_SESSION['csrf_plain'] = bin2hex(random_bytes(32)); // Store plain token
    $_SESSION['csrf_hash'] = password_hash($_SESSION['csrf_plain'], PASSWORD_DEFAULT); // Store hashed token
}


if (!isset($_SESSION['username']) || ($_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = htmlspecialchars($_POST['token'] ?? '', ENT_QUOTES, 'UTF-8');
    
    // Validate CSRF token
    if (!$token || !password_verify($token, $_SESSION['csrf_hash'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }

    // CSRF token is valid - Now unset it to prevent reuse
    unset($_SESSION['csrf_plain']);
    unset($_SESSION['csrf_hash']);

    // Regenerate new CSRF token for next request
    $_SESSION['csrf_plain'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_hash'] = password_hash($_SESSION['csrf_plain'], PASSWORD_DEFAULT);

    $name = $_POST['name'];
    $code = $_POST['code'];
    $department_name = $_POST['department_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Check for special characters in Course Name and Course Code
    if (preg_match('/[^a-zA-Z0-9\s]/', $name)) {
        echo "<script>alert('Error: Course Name cannot contain special characters.');</script>";
    } elseif (preg_match('/[^a-zA-Z0-9\s]/', $code)) {
        echo "<script>alert('Error: Course Code cannot contain special characters.');</script>";
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $error = "Start date cannot be later than the End date. Try again.";
    } elseif (strtotime($start_date) == strtotime($end_date)) {
        $error = "Start Date cannot be the same as End Date. Try again.";
    } else {
        // Insert course into the database
        $sql = "INSERT INTO course (NAME, CODE, DEPARTMENT_NAME, START_DATE, END_DATE) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $code, $department_name, $start_date, $end_date);

        if ($stmt->execute()) {
            header("Location: view_course.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
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
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #2c2e3a;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            margin-top: 50px;
            margin:50px auto;
            max-width: 500px;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            height: 700px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 500;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52,152,219,0.2);
        }

        button[type="submit"]{
            display: block;
            width: 100%;
            background: #fff;
            color: black;
            padding: 10px;
            border: 1px solid #2c2e3a;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 15px;
            text-align: center;
            font-size: 15px;
            margin-left: 10px;
        }

        button[type="submit"]:hover {
            margin-top: 15px;
            background: #3b3ec0;
            color: white;
            outline: 1px solid #fff;
        }

        button {
            display: block;
            width: 100%;
            background: #fff;
            color: black;
            padding: 10px;
            border: 1px solid #2c2e3a;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 15px;
            text-align: center;
            font-size: 15px;
            margin-left: -50px;
        }

        button:hover {
            margin-top: 15px;
            background: #3b3ec0;
            color: white;
            outline: 1px solid #fff;
        }

        .back-link {
            /* display: block; */
            text-align: center;
            margin-left: 60px;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        .error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 4px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<a href="view_course.php" class="back-link"><button>Back to Course Dashboard</button></a>
<body>

    <div class="container">
        <h2>Create New Course</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Course Name</label>
                <input type="text" name="name" placeholder="Enter course name" required>
            </div>

            <div class="form-group">
                <label>Course Code</label>
                <input type="text" name="code" placeholder="Enter course code" required>
            </div>

            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department_name" placeholder="Enter department name" required>
            </div>

            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" required>
            </div>

            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" required>
            </div>

            <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_plain'] ?? '') ?>">

            <button type="submit">Create Course</button>
        </form>

    </div>
</body>
</html>
<?php $conn->close(); ?>