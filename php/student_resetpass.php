<?php
// Start session
session_start();
require 'functions.php';
$conn = db_connect();

// Generate and store a new CSRF token if it doesn't exist
if (empty($_SESSION['csrf_plain'])) {
    $_SESSION['csrf_plain'] = bin2hex(random_bytes(32)); // Store plain token
    $_SESSION['csrf_hash'] = password_hash($_SESSION['csrf_plain'], PASSWORD_DEFAULT); // Store hashed token
}

// Ensure student is logged in
if (!isset($_SESSION['id'])) {
    die("Access denied. Please log in.");
}

$student_id = $_SESSION['id'];
$error = $success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    
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

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } else {
        // Fetch existing password from the database
        $stmt = $conn->prepare("SELECT password_hash FROM students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($password_hash);
            $stmt->fetch();

            // Verify current password
            if (!password_verify($current_password, $password_hash)) {
                $error = "Current password is incorrect!";
            } else {
                // Hash the new password
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update the password in the database
                $update_stmt = $conn->prepare("UPDATE students SET password_hash = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_hashed_password, $student_id);

                if ($update_stmt->execute()) {
                    $success = "Password updated successfully!";
                } else {
                    $error = "Error updating password!";
                }
            }
        } else {
            $error = "User not found!";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        * {
            margin: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #b3b4bd;
        }
        .container {
            width: 350px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            margin-bottom: 15px;
            color: #0a21c0;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #0a21c0;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            border-radius: 5px;
        }
        button:hover {
            background: #050a44;
        }
        .message {
            margin-top: 10px;
            font-size: 14px;
            color: red;
        }
        .success {
            color: green;
        }
        .back-button {
            background: #fff;
            color: black;
            padding: 10px;
            border: 1px solid #2c2e3a;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
        }
        .back-button:hover {
            background: #3b3ec0;
            color: white;
            outline: 1px solid #fff;
        }
    </style>
</head>
<body>

    <!-- Change Password Form -->
    <div class="container">
        <h2>Change Password</h2>
        <?php if (!empty($error)) echo "<p class='message'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p class='message success'>$success</p>"; ?>
        <form method="post">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_plain'] ?? '') ?>">
            <button type="submit">Update Password</button>
        </form>

        <!-- Back Button (Now Below the Form) -->
        <a href="student_dashboard.php"><button class="back-button">Back</button></a>
    </div>

</body>
</html>
