<?php

session_start();



?>


<form method="POST">
    class name: <input type="text" name="username" required><br>

    <input type = "radio" name= "mode" id ="semester"value= "semester"/>
    <label for = "semester">Semester</label>
    <input type = "radio" name= "mode" id ="term"value= "term"/>
    <label for = "term">Term</label>

</form>