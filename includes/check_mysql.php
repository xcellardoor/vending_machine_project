<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 06/01/15
 * Time: 19:27
 */

include('./includes/credentials.php');

error_reporting(E_ERROR);
$db_handle = mysql_connect($server, $user_name, $password);
//$db_found = mysql_select_db($database, $db_handle);

if (!$db_handle) {
    die('Connection failure: '. mysql_error());
}
echo 'Connected successfully';
mysql_close($db_handle);
?>