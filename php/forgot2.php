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
// Start with PHPMailer class

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
$mail->Body = '<html>Click <a href="http://example.com/reset-password.php?token">here</a>
        to reset your password.</html>';
$mail->AltBody = 'Hi there, we are happy to confirm your booking. Please check the document in the attachment.';
// add attachment 
// just add the '/path/to/file.pdf'
$attachmentPath = './confirmations/yourbooking.pdf';
if (file_exists($attachmentPath)) {
    $mail->addAttachment($attachmentPath, 'yourbooking.pdf');
}

// send the message
if(!$mail->send()){
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}}
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
