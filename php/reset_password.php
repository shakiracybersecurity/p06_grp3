<?php
require "functions.php";
$conn = db_connect();
session_start();

// Generate and store a new CSRF token if it doesn't exist
if (empty($_SESSION['csrf_plain'])) {
    $_SESSION['csrf_plain'] = bin2hex(random_bytes(32)); // Store plain token
    $_SESSION['csrf_hash'] = password_hash($_SESSION['csrf_plain'], PASSWORD_DEFAULT); // Store hashed token
}

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT student_id, staff_id, admin_id, token_expiry from password_resets WHERE RESET_TOKEN = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$now = date("Y-m-d H:i:s"); 
if ($now > $user['token_expiry']){ //checks if the reset token is expired
    header("Location: expired.php");
}

if (isset($user['student_id'])) {
    $role = 'students';
    $user_id = $user['student_id'];
}elseif (isset($user['staff_id'])) {
    $role = 'faculty';
    $user_id = $user['staff_id'];
}elseif (isset($user['admin_id'])) {
    $role = 'admins';
    $user_id = $user['admin_id'];
}else{
    header("Location: expired.php");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
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

    $pass = htmlspecialchars(trim($_POST['password']));
    $confpass = htmlspecialchars(trim($_POST['conf_password']));

    if ($pass == $confpass){
        $passHash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE $role SET password_hash = ? WHERE id = ?"); //update password
        $stmt -> bind_param("si", $passHash, $user_id);
        $stmt -> execute();
        $stmt -> close();

        $stmt = $conn->prepare("DELETE FROM password_resets WHERE reset_token = ?");
        $stmt->bind_param("s", $token);
        $stmt -> execute();
        $stmt -> close();

        header("Location: reset_success.php");
    }else{
        $error = "passwords do not match!";
    }
}
?>

<!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content="width=device-width, initial-scale=1.0">
        <title> Reset Password</title>
 <style>
    body{
    margin: 0;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-image: url('background.jpeg');
    background-size: cover;
}
*{
    margin: 0;
    box-sizing: border-box;
    font-family: sans-serif;
}
.container{
    margin-top: 0px;
    margin:50px auto;
    max-width: 500px;
    height: 500px;
    background-color: #fff;
    padding: 30px;
    box-shadow: 0 0px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    border: 1px solid #fff;
}
h2{
    text-align: center;
    color: #2c2e3a;
    margin-top: 30px;
    margin-bottom: 20px;
}

form{
    display: flex;
    flex-direction: column;
    margin-top: 20px;
}
label{
    font-size: 18px;
    margin-bottom: 5px;
}
input[type="password"]{
    padding: 10px;
    margin-top: 25px;
    border: none;
    border-radius: 10px;
    background: transparent;
    border: 1px solid #2c2e3a;
    color: #141619;
    font-size: 13px;
}
button {
    background: #fff;
    color: black;
    padding: 10px;
    border: 1px solid #2c2e3a;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 15px;
}
button:hover {
    margin-top: 20px;
    background: #3b3ec0;
    color: white;
    outline: 1px solid #fff;
}
</style>
</head>
<body>
<div class = "container">
<form method="POST">
    <h2>Password Reset</h2>
    
    <?php if (isset($success)){echo $success; }?>
    <label for ="password"> enter new password:</label>
    <input type = "password" id ="password" name ="password" required><br>
    <label for ="conf_password"> confirm password:</label>
    <input type = "password" id ="conf_password" name ="conf_password" required>
    <br>
    <?php if (isset($error)) {echo $error;}?>
    <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_plain'] ?? '') ?>">
    <button type = "submit"> Reset </button> 
</div>
</Body>
</html>

