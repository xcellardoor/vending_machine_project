<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 18/12/14
 * Time: 15:10
 */

include('credentials.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database,$db_handle);

$SQL = "SELECT SUM(profit_made) AS Profit, product_table.product_name AS 'Product Name' FROM
sales_table INNER JOIN product_table ON sales_table.product_id=product_table.product_id group by
product_name ORDER BY Profit DESC;";

$result = mysql_query($SQL);

while($db_field = mysql_fetch_assoc($result)) {
    #array_push($product_array_values, $db_field['product_id']);
    print $db_field['product_name']; print "<br>";
}


?>