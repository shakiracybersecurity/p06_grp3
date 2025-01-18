<?php
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

    //Check if the email exists in the users table
    $stmt = $conn->prepare("SELECT email FROM students WHERE email= ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result ->num_rows ===1){
        //Generate a unique token
        $reset_token = bin2hex(random_bytes(32));
        $token_expiry = date("Y-m-d H-i-s", strtotime("+1 hour"));

        //Insert token into passwords_resets table
        $insertStmt = $conn ->prepare("INSERT INTO password_resets (email, reset_token, token_expiry) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $email,$reset_token,$token_expiry);
        $insertStmt -> execute();

        //send email with the reset link
        $mail= new PHPMailer(true);
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail ->SMTPAuth = true;
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Username = "no-reply@roboticcourse.com";
        $mail->Password = "roboticcourse";

        $mail->isHtml(true);

        return $mail;
     
        $mail->setFrom("noreply@roboticcourse.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END

        Click <a href="http://example.com/reset-password.php?token">here</a>
        to reset your password.

        END;
        try{
            $mail->send();
        } catch (Exception $e){
            echo"Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
        echo "Message sent, please check your inbox.";
}  
}
?>
<!-- Login form -->
<form method="POST">
<title>Forgot Password</title>
<?php if (isset($error)) {echo $error;}?>
<?php if (isset($success)){echo $success; }?>
<label for ="email"> Enter your email address:</label>
<input type = "email"id ="email" name ="email" required>
<br>
<button type = "submit"> Send Reset Link </button> 
