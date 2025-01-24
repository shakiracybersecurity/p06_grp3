<?php
require 'C:\xampp\htdocs\p06_grp3\PHPMailer-master\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\p06_grp3\PHPMailer-master\PHPMailer-master\src\Exception.php';
require 'C:\xampp\htdocs\p06_grp3\PHPMailer-master\PHPMailer-master\src\SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


// Database connection details
$host = 'localhost';
$dbname = 'robotic course management'; // Updated for clarity
$user = 'root';
$pass = '';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT email, id FROM students WHERE email= ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
// Start with PHPMailer class


    if ($result ->num_rows ===1){

        //token
        $length = 16;
        $token = bin2hex(random_bytes($length));
        $token_expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        $stmt=$conn->prepare("INSERT INTO password_resets (student_id, email, reset_token, token_expiry) VALUES (?, ?, ?, ?)");
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
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent, please check your inbox';
        }    
}else{
    echo "email is not registered";
}
}
?>

<!-- Login form -->
<form method="POST">
<title>Forgot Password</title>
<h2>Forgot Password</h2>
<?php if (isset($error)) {echo $error;}?>
<?php if (isset($success)){echo $success; }?>
<label for ="email"> Enter your email address:</label>
<input type = "email"id ="email" name ="email" required>
<br>
<button type = "submit"> Send Reset Link </button>
<br>
<a href="login.php">Back to Login Page</a>
