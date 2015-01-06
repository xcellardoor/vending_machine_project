<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 18/12/14
 * Time: 16:34
 */

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

    /**$SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id order by $order_instruction;";
     *
     * //Override rules to get the table to show what we actually want.
     * if ($vending_filter_dropdown == "machine_id") {
     * if ($_REQUEST['vending_filter_machine_id'] != "") {
     * $machine_id = $_REQUEST['vending_filter_machine_id'];
     * $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id WHERE machine_table.machine_id=$machine_id order by $order_instruction;";
     * }
     * }
     *
     * if ($vending_filter_dropdown == "out_of_date") {
     * $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id WHERE vending_table.best_before<=CURDATE() order by $order_instruction;";
     * }
     *
     * if ($vending_filter_dropdown == "in_date") {
     * $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id WHERE vending_table.best_before>=CURDATE() order by $order_instruction;";
     * }
     *
     * if ($vending_filter_dropdown == "product_name") {
     * $product_name = $_REQUEST['vending_filter_product_name'];
     * $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id WHERE product_table.product_name LIKE '$product_name%' order by $order_instruction;";
     * }
     *
     * if ($vending_filter_dropdown == "building") {
     * $building = $_REQUEST['vending_filter_building'];
     * $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id WHERE machine_table.building LIKE '$building%' order by $order_instruction;";
     * }
     * /**else{
     *
     *
     * $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id order by $order_instruction;";
     * }**/

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