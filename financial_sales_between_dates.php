<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 18/12/14
 * Time: 16:34
 */


$older_date = $_REQUEST["older_date"];
$newer_date = $_REQUEST["newer_date"];



include('credentials.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database,$db_handle);

$SQL = "SELECT SUM(profit_made) AS Profit FROM sales_table where date_of_sale > '$older_date' AND date_of_sale < '$newer_date';";
#$SQL = "SELECT * from sales_table;";

$result = mysql_query($SQL);

$reply="";

while($db_field = mysql_fetch_assoc($result)) {
    #array_push($product_array_values, $db_field['product_id']);
    $reply .= $db_field['Profit']."<br>";
}

#echo $older_date." ";
#echo $newer_date;
if($reply > 0){
    echo $reply;
}
else{
    echo "Result is 0";
}