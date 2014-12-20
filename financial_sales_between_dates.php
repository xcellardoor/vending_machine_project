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

#$SQL = "SELECT SUM(profit_made) AS Profit FROM sales_table where date_of_sale > '$older_date' AND date_of_sale < '$newer_date';";
#$SQL = "SELECT * from sales_table;";
$SQL = "SELECT * FROM sales_table INNER JOIN product_table ON sales_table.product_id=product_table.product_id WHERE date_of_sale > '$older_date' AND date_of_sale < '$newer_date';";

$total_profit_query = "SELECT SUM(profit_made) AS total_profit FROM sales_table WHERE date_of_sale > '$older_date' AND date_of_sale < '$newer_date';";

$total_profit_result = mysql_query($total_profit_query);

$total_profit = mysql_fetch_assoc($total_profit_result);

$result = mysql_query($SQL);

$reply="<table>";
$reply.="<th>Sale Number<th>Product Name<th>Date of Sale <th>Total: ".$total_profit['total_profit']."</th>";


while($db_field = mysql_fetch_assoc($result)) {
    #$reply .= $db_field['Profit']."<br>";
    $reply .= "<tr><td>".$db_field['sale_number']."<td>".$db_field['product_name']."<td>".$db_field['date_of_sale']."<td>".$db_field['profit_made']."</td></tr>";
}

$reply .="</table>";
#echo $older_date." ";
#echo $newer_date;
if(strlen($reply) > 15){
    echo $reply;
}
else{
    echo "<h2>No Results</h2>";
}