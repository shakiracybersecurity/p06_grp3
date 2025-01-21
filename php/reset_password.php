<form method="POST">
<title>Password reset</title>
<h2>Password Reset</h2>
<?php if (isset($error)) {echo $error;}?>
<?php if (isset($success)){echo $success; }?>
<label for ="password"> enter new password:</label>
<input type = "text"id ="password" name ="password" required><br>
<label for ="password"> confirm password:</label>
<input type = "text"id ="password" name ="password" required>
<br>
<button type = "submit"> Reset </button> 