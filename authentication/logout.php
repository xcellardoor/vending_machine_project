<?php
//Make this PHP script aware of the existing SESSION, and then destory it so the user no longer has an authenticated SESSIONID.
session_start();
session_destroy();

echo "You've been logged out!";

//Give the user a link to click if they want to log back in.
echo "<br><a href='../index.php'>Log in!</a>";