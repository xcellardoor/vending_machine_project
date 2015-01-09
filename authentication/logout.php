<?php
session_start();
session_destroy();

echo "You've been logged out!";

echo "<br><a href='../index.php'>Log in!</a>";

//Add window close?