<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 29/12/14
 * Time: 19:49
 */

if (isset($_REQUEST["financial_sort_by_dropdown"])) {

    $sort_by_instruction = $_REQUEST["financial_sort_by_dropdown"];
} else {
    $sort_by_instruction = "sale_number";
}

include('./includes/credentials.php');
include('./includes/shared_php_functions.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database, $db_handle);

$SQL = "SELECT SUM(profit_made) AS Profit FROM sales_table;";

$result = mysql_query($SQL);

setlocale(LC_MONETARY, 'en_GB.UTF-8');

while ($db_field = mysql_fetch_assoc($result)) {
    $all_time_cash = money_format('%n', ($db_field['Profit'] / 100));
}

$reply = "<table id='financial_table' class='tablesorter'><thead><tr><th colspan='4'>All Time Profit: $all_time_cash</th></tr><tr><th>Sale Number</th><th>Product Name</th><th>Sale Date</th><th>Profit</th></tr></thead><tbody>";
//$SQL = "SELECT * FROM sales_table INNER JOIN product_table ON sales_table.product_id=product_table.product_id order by sale_number ASC;";
$SQL = "SELECT * FROM sales_table;";

if ($sort_by_instruction == 'popularity') {

    $older_date = $_REQUEST['popularity_older_date'];
    $newer_date = $_REQUEST['popularity_newer_date'];

    $SQL = "SELECT SUM(profit_made) AS profit_between_dates FROM sales_table WHERE date_of_sale>='$older_date' AND date_of_sale<='$newer_date';";
    $profit_between_dates = "";
    $result = mysql_query($SQL);
    while ($db_field = mysql_fetch_assoc($result)) {
        $profit_between_dates .= money_format('%n', ($db_field['profit_between_dates'] / 100));
    }

    $reply = "<table id='financial_table' class='tablesorter'><thead><tr><th colspan='4'>All Time Profit: $all_time_cash</th></tr><tr><th colspan='4'>Profit Between Dates: $profit_between_dates </th></tr><tr><th>Product Name</th><th>Amount Sold</th></tr></thead><tbody>";
    //INNER JOIN product_table ON sales_table.product_id=product_table.product_id
    $SQL = "SELECT COUNT(*), product_table.product_name from sales_table WHERE date_of_sale>='$older_date' AND date_of_sale<='$newer_date'group by sales_table.product_id ORDER BY COUNT(*) DESC;";

    $result = mysql_query($SQL);
    while ($db_field = mysql_fetch_assoc($result)) {
        $reply .= "<tr><td>" . $db_field['product_name'] . "</td><td>" . $db_field['COUNT(*)'] . "</td></tr>";
    }

} elseif ($sort_by_instruction == 'name') {
    $name = $_REQUEST['name_value'];
    //INNER JOIN product_table ON sales_table.product_id=product_table.product_id
    $SQL = "SELECT * FROM sales_table WHERE product_table.product_name LIKE '$name%' order by sale_number;";

    $result = mysql_query($SQL);

    while ($db_field = mysql_fetch_assoc($result)) {
        $reply .= "<tr><td>" . $db_field['sale_number'] . "</td><td>" . $db_field['product_name'] . "</td><td>" . $db_field['date_of_sale'] . "</td><td>" . money_format('%n', ($db_field['profit_made'] / 100)) . "</td></tr>";
    }

} elseif ($sort_by_instruction == "between_dates") {
    $older_date = $_REQUEST["between_dates_older_date"];
    $newer_date = $_REQUEST["between_dates_newer_date"];
    //INNER JOIN product_table ON sales_table.product_id=product_table.product_id
    $SQL = "SELECT * FROM sales_table WHERE date_of_sale>='$older_date' AND date_of_sale<='$newer_date' order by date_of_sale DESC;";

    $result = mysql_query($SQL);

    while ($db_field = mysql_fetch_assoc($result)) {
        $reply .= "<tr><td>" . $db_field['sale_number'] . "</td><td>" . $db_field['product_name'] . "</td><td>" . $db_field['date_of_sale'] . "</td><td>" . money_format('%n', ($db_field['profit_made'] / 100)) . "</td></tr>";
    }

} else {
    $result = mysql_query($SQL);

    while ($db_field = mysql_fetch_assoc($result)) {
        $reply .= "<tr><td>" . $db_field['sale_number'] . "</td><td>" . $db_field['product_name'] . "</td><td>" . $db_field['date_of_sale'] . "</td><td>" . money_format('%n', ($db_field['profit_made'] / 100)) . "</td></tr>";
    }
}


$reply .= "</tbody></table>";

echo $reply;
