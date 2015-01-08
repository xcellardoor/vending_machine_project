<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<HTML>
<head>
    <link rel="stylesheet" type="text/css" href="../css/stylesheet.css">
</head>
<BODY>
<div id="login_box">
    <h2>Vending Machine Management System</h2>

    <h3>Login</h3>

    <form name='login_form' method='post' action='login_verification.php'>
        Username:<input type="text" name="user_user_input" id="user_user_input" size="20"><br>
        Password:<input type="password" name="user_password_input" id="user_password_input" size="20"><br>
        <input name="login_form_submit" type="submit" value="Login"/>

    </form>
</div>
</BODY>
</HTML>