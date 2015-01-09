<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 18/12/14
 * Time: 16:34
 */
error_reporting(E_ERROR);


//This IF ELSE allows this page to be included and a default value of machine_id to be used for sorting if no other is passed
if (isset($_REQUEST["stockroom_sort_by_dropdown"])) {
    $order_instruction = $_REQUEST["stockroom_sort_by_dropdown"];
} else {
    $order_instruction = "machine_id";
}

if (isset($_REQUEST['stockroom_filter_dropdown'])) {
    $stockroom_filter_dropdown = $_REQUEST['stockroom_filter_dropdown'];
} else {
    $stockroom_filter_dropdown = "unassigned";
}

include('./includes/credentials.php');

//$db_handle = mysql_connect($server, $user_name, $password);
//$db_found = mysql_select_db($database, $db_handle);

$connection = new mysqli($server, $user_name, $password, $database);


date_default_timezone_set('Europe/London');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
    $SQL = "SELECT * FROM product_table;";

    //Override rules to get the table to show what we actually want.

    if ($stockroom_filter_dropdown == "remaining_stock") {
        $stock_value = $_REQUEST['stockroom_filter_stock_value'];
        $direction = $_REQUEST['stockroom_filter_stock_direction'];

        //Reassign $direction to something that SQL can use
        if ($direction == "lt") {
            $direction = "<=";
        } else {
            $direction = ">=";
        }

        $SQL = "SELECT * from product_table WHERE remaining_stock$direction$stock_value;";

    }

    if ($stockroom_filter_dropdown == "product_name") {
        $product_name = $_REQUEST['stockroom_filter_product_name'];
        $SQL = "SELECT * from product_table WHERE product_name LIKE '$product_name%';";
    }

    if ($stockroom_filter_dropdown == "in_stock") {
        $SQL = "SELECT * from product_table WHERE remaining_stock>low_stock_alert;";
    }
    if ($stockroom_filter_dropdown == "out_of_stock") {
        $SQL = "SELECT * from product_table WHERE remaining_stock<=low_stock_alert;";
    }
    if ($stockroom_filter_dropdown == "low_stock") {
        $low_stock = $_REQUEST['stockroom_filter_low_stock'];
        $SQL = "SELECT * from product_table WHERE low_stock LIKE '$low_stock%';";
    }

    $reply = "";

$result = $connection->query($SQL);
        $reply .= "<table class='tablesorter' id='stockroom_table'><thead><tr><th>Product Name<th>Remaining Stock<th>Purchase Price<th>Sale price<th>Low Stock Alert<th>Stock State</th></tr></thead><tbody>";
        while ($db_field = $result->fetch_assoc()) {
            $reply .= "<tr><td>" . $db_field['product_name'] . "<td>" . $db_field['remaining_stock'] . "<td>" . $db_field['stock_purchase_price'] . "<td>" . $db_field['stock_sale_price'] . "<td>" . $db_field['low_stock_alert'];

            if ($db_field['low_stock_alert'] > $db_field['remaining_stock']) {
                $reply .= "<td BGCOLOR=\"#EE0000\">LOW!</td></tr>";
            } else {
                $reply .= "<td BGCOLOR=\"#26D82F\">OK</td></tr>";
            }
        }

    $reply .= "</tbody></table>";

if (strlen($reply) > 15) {
    echo $reply;
} else {
    echo "<h2>No Results</h2>";
}