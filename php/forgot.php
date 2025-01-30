<?php
require 'C:\xampp\htdocs\p06_grp3\PHPMailer-master\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\p06_grp3\PHPMailer-master\PHPMailer-master\src\Exception.php';
require 'C:\xampp\htdocs\p06_grp3\PHPMailer-master\PHPMailer-master\src\SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'functions.php';
$conn = db_connect();

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $users = array('students', 'faculty', 'admins');

    foreach($users as $users){
        $stmt = $conn->prepare("SELECT email, id FROM $users WHERE email= ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result ->num_rows ===1){
            $role = $users;
            break;
        }
    }
    
    
// Start with PHPMailer class


    if ($result ->num_rows ===1){
        //token
        $length = 16;
        $token = bin2hex(random_bytes($length));
        $token_expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        if ($role == 'students'){
            $stmt=$conn->prepare("INSERT INTO password_resets (student_id, email, reset_token, token_expiry) VALUES (?, ?, ?, ?)");
        }elseif($role == 'faculty'){
            $stmt=$conn->prepare("INSERT INTO password_resets (staff_id, email, reset_token, token_expiry) VALUES (?, ?, ?, ?)");
        }elseif ($role == 'admins') {
            $stmt=$conn->prepare("INSERT INTO password_resets (admin_id, email, reset_token, token_expiry) VALUES (?, ?, ?, ?)");
        }
        
        $stmt->bind_param("isss", $user_id,$email,$token,$token_expiry);
        $stmt -> execute();


        //mail stuffs
        // create a new object
        $mail = new PHPMailer();
        // configure an SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'robotictp@gmail.com';
        $mail->Password = 'pboi xxfi zxrv atuk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('robotictp@gmail.com');
        $mail->addAddress($email);
        $mail->Subject = 'password reset';
        // Set HTML 
        $mail->isHTML(TRUE);
        $mail->Body = '<html>Click <a href="http://localhost/p06_grp3/php/reset_password.php?token=' . $token . '">here</a>
                to reset your password.</html>';
        $mail->AltBody = 'Hi there, we are happy to confirm your booking. Please check the document in the attachment.';

        // send the message
        if(!$mail->send()){
            $msg = 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $msg = 'Message has been sent, please check your inbox!';
        }    
}else{
    $msg = "email is not registered";
}
}
?>

<!-- Login form -->
<!DOCTYPE html>
 <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name ="viewport" content="width=device-width, initial-scale=1.0">
        <title> Forgot Password</title>
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
input[type="text"]{
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
        <?php if (isset($msg)) {echo $msg;}?>
        <h2> Forgot Password/ First Time Login</h2>
        <form method="POST">
            <?php if (isset($error)) {echo $error;}?>
            <?php if (isset($success)){echo $success; }?>
           <label for ="email"> Enter your Email Address:</label>
           <input type = "email"id ="email" name ="email"required placeholder="Email Address">
           <br>
           
           <button type = "submit"> Send Reset Link </button>
           <br>
        </form>   
<a href="login.php"><button>Back to login page</button></a>

</div>
</body>