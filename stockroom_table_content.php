<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 18/12/14
 * Time: 16:34
 */

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

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database, $db_handle);


date_default_timezone_set('Europe/London');

if ($db_found) {

    $SQL = "SELECT * FROM product_table;";

    //Override rules to get the table to show what we actually want.


    /*if ($vending_filter_dropdown == "building") {
        $building = $_REQUEST['vending_filter_building'];
        $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id WHERE machine_table.building LIKE '$building%' order by $order_instruction;";
    }*/

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
    /**else{
     *
     *
     * $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id order by $order_instruction;";
     * }**/

    $result = mysql_query($SQL);

    $reply = "";

    if ($db_found) {
        $result = mysql_query($SQL);
        $reply .= "<table class='tablesorter' id='stockroom_table'><thead><tr><th>Product Name<th>Remaining Stock<th>Purchase Price<th>Sale price<th>Low Stock Alert<th>Stock State</th></tr></thead><tbody>";
        while ($db_field = mysql_fetch_assoc($result)) {
            $reply .= "<tr><td>" . $db_field['product_name'] . "<td>" . $db_field['remaining_stock'] . "<td>" . $db_field['stock_purchase_price'] . "<td>" . $db_field['stock_sale_price'] . "<td>" . $db_field['low_stock_alert'];

            if ($db_field['low_stock_alert'] > $db_field['remaining_stock']) {
                $reply .= "<td BGCOLOR=\"#EE0000\">LOW!</td></tr>";
            } else {
                $reply .= "<td BGCOLOR=\"#26D82F\">OK</td></tr>";
            }
        }
        //MYSQL CLOSE could go here
    } else {
        $reply .= "Database Access Error!";
        mysql_close($db_handle);

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