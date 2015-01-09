<?php
//This IF ELSE allows this page to be included and a default value of machine_id to be used for sorting if no other is passed
if (isset($_REQUEST["vending_sort_by_dropdown"])) {
    $order_instruction = $_REQUEST["vending_sort_by_dropdown"];
} else {
    $order_instruction = "machine_id";
}

if (isset($_REQUEST['vending_filter_dropdown'])) {
    $vending_filter_dropdown = $_REQUEST['vending_filter_dropdown'];
} else {
    $vending_filter_dropdown = "unassigned";
}

include('./includes/credentials.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database, $db_handle);

if ($_REQUEST['create_from_low_stock'] = '1') {
    $SQL = "SELECT * FROM product_table WHERE remaining_stock<=low_stock_alert;";
}

date_default_timezone_set('Europe/London');

if ($db_found) {
    $result = mysql_query($SQL);

    $reply = "<table id='table' class='tablesorter'>";
    $reply .= "<thead><tr><th>Product Name</th></tr></thead><tbody>";

    while ($db_field = mysql_fetch_assoc($result)) {
        $reply .= "<tr><td>" . $db_field['product_name'] . "<td BGCOLOR=\"#EE0000\">LOW STOCK</td></tr>";

    }

    $reply .= "</tbody></table>";
} else {
    echo "Error";
}

if (strlen($reply) > 15) {
    echo $reply;
} else {
    echo "<h2>No Results</h2>";
}