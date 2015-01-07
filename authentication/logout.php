<?php
session_start();
session_destroy();


/**

 * Created by PhpStorm.
 * User: cellardoor
 * Date: 07/01/15
 * Time: 16:20
 */

echo "You've been logged out!";

echo "<br><a href='../index.php'>Log in!</a>";

//Add window close?