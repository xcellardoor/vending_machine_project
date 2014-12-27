<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 18/12/14
 * Time: 16:34
 */

//This IF ELSE allows this page to be included and a default value of machine_id to be used for sorting if no other is passed
if(isset($_REQUEST["vending_sort_by_dropdown"])){
    $order_instruction = $_REQUEST["vending_sort_by_dropdown"];
    }
else{
    $order_instruction = "machine_id";
}

include('credentials.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database,$db_handle);


date_default_timezone_set('Europe/London');

if($db_found) {


    $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, machine_table.building, machine_table.floor, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id INNER JOIN machine_table ON vending_table.machine_id=machine_table.machine_id order by $order_instruction;";

    $result = mysql_query($SQL);

    $reply = "<table>";
    $reply .= "<th>Machine<th>Product Name<th>Quantity<th>Building<th>Floor<th>Best Before</th>";


    while ($db_field = mysql_fetch_assoc($result)) {
        $reply .= "<tr><td>" . $db_field['machine_id'] . "<td>" . $db_field['product_name'] . "<td>" . $db_field['quantity_in_machine']."<td>". $db_field['building']."<td>".$db_field['floor'];

        if (strtotime($db_field['best_before']) < strtotime('now')) {
            $reply.= "<td BGCOLOR=\"#EE0000\">OUT OF DATE!</td></tr>";
        } else {
            $reply.= "<td BGCOLOR=\"#00ff00\">IN DATE</td></tr>";
        }
    }

    $reply .= "</table>";
#echo $older_date." ";
#echo $newer_date;
}
else{
    echo "Error";
}

if(strlen($reply) > 15){
    echo $reply;
}
else{
    echo "<h2>No Results</h2>";
}