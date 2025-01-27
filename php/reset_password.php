<?php
require "functions.php";
$conn = db_connect();
session_start();

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT student_id, staff_id, admin_id from password_resets WHERE RESET_TOKEN = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

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

//$user_id = $user['student_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
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
        echo "passwords do not match";
    }
}
?>

<form method="POST">
<title>Password reset</title>
<h2>Password Reset</h2>
<?php if (isset($error)) {echo $error;}?>
<?php if (isset($success)){echo $success; }?>
<label for ="password"> enter new password:</label>
<input type = "password" id ="password" name ="password" required><br>
<label for ="conf_password"> confirm password:</label>
<input type = "password" id ="conf_password" name ="conf_password" required>
<br>
<button type = "submit"> Reset </button> 

