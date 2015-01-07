
<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 07/01/15
 * Time: 14:30
 */

if (session_status() === PHP_SESSION_NONE){session_start();}


/*if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == "true") {
    //header("location:index.php");
} else {



}*/
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
    Username:<input type="text" name="user_user_input" id="user_user_input">
    Password:<input type="password" name="user_password_input" id="user_password_input">
    <input name="login_form_submit" type="submit" value="Login"/>

    </form>
</div>
</BODY>



</HTML>