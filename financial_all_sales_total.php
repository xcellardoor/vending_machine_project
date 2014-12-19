<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 18/12/14
 * Time: 16:34
 */

include('credentials.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database,$db_handle);

$SQL = "SELECT SUM(profit_made) AS Profit FROM sales_table;";

$result = mysql_query($SQL);

while($db_field = mysql_fetch_assoc($result)) {
    #array_push($product_array_values, $db_field['product_id']);
    print $db_field['Profit']."<br>";
}