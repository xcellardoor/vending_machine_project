<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 29/12/14
 * Time: 19:49
 */

if(isset($_REQUEST["financial_sort_by_dropdown"])){
    switch($_REQUEST["financial_sort_by_dropdown"]){
        case "sale_number":
            $order_instruction="sale_number";
            return;
        case "popularity_descending":

            return;

        default:

    }
    $order_instruction = $_REQUEST["financial_sort_by_dropdown"];
}

else {
    $order_instruction = "sale_number";
}

include('./includes/credentials.php');
include('./includes/shared_php_functions.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database,$db_handle);

$SQL = "SELECT SUM(profit_made) AS Profit FROM sales_table;";

$result = mysql_query($SQL);

setlocale(LC_MONETARY, 'en_GB.UTF-8');

while($db_field = mysql_fetch_assoc($result)) {
    #array_push($product_array_values, $db_field['product_id']);
    $all_time_cash= money_format('%n', ($db_field['Profit']/100));
    }


if((isset($_REQUEST["older_date"]))&(isset($_REQUEST["newer_date"]))){
    $older_date=$_REQUEST["older_date"];
    $newer_date=$_REQUEST["newer_date"];
    $SQL = "SELECT * FROM sales_table INNER JOIN product_table ON sales_table.product_id=product_table.product_id WHERE date_of_sale>='$older_date' AND date_of_sale<='$newer_date' order by date_of_sale DESC;";

}

else {
    $SQL = "SELECT * FROM sales_table INNER JOIN product_table ON sales_table.product_id=product_table.product_id order by $order_instruction;";
}
$result = mysql_query($SQL);

$reply="<table id='financial_table' class='tablesorter'><thead><tr><th colspan='4'>All Time Profit: $all_time_cash</th></tr><tr><th>Sale Number</th><th>Product Name</th><th>Date of Sale</th><th>Profit Made</th></tr></thead><tbody>";

while($db_field = mysql_fetch_assoc($result)){
    $reply.="<tr><td>".$db_field['sale_number']."</td><td>".$db_field['product_name']."</td><td>".$db_field['date_of_sale']."</td><td>".money_format('%n', ($db_field['profit_made']/100))."</td></tr>";
}

$reply.="</tbody></table>";

echo $reply;

?>