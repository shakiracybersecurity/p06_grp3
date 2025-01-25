<?php
require "functions.php";

$conn = db_connect();
session_start();

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

is_logged_in([3]);

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }

    $delete = $_POST['delete'];
    
    if ($delete == "delete"){
        $stmt = $conn->prepare("DELETE FROM class WHERE id = ?");
        $stmt -> bind_param("i", $id);
        $stmt -> execute();
        $stmt -> close();
        echo "deleted";
        header("Location: viewclass.php");
    } else {
        header("Location: viewclass.php");
    }    
}
?>

<form method="post"> 
    <input type="submit" name="delete"
            class="button" value="delete" /> 

    <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
    <input type="submit" name="delete"
            class="button" value="no" /> 
</form>