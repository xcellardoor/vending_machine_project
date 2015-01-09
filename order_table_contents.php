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

//$db_handle = mysql_connect($server, $user_name, $password);
//$db_found = mysql_select_db($database, $db_handle);

$connection = new mysqli($server, $user_name, $password, $database);
if($connection->connect_error){
    die ("Connection failed: " . $connection->connect_error);
}
$result = $connection->query($SQL);


if ($_REQUEST['create_from_low_stock'] = '1') {
    $SQL = "SELECT * FROM product_table WHERE remaining_stock<=low_stock_alert;";
}

date_default_timezone_set('Europe/London');

    $result = $connection->query($SQL);

    $reply = "<table id='table' class='tablesorter'>";
    $reply .= "<thead><tr><th>Product Name</th></tr></thead><tbody><td BGCOLOR='#EE0000'>LOW STOCK</td>";

    while ($db_field = $result->fetch_assoc()) {
        $reply .= "<tr><td>" . $db_field['product_name']."</tr>";

    }

    $reply .= "</tbody></table>";

if (strlen($reply) > 15) {
    echo $reply;
} else {
    echo "<h2>No Results</h2>";
}

$connection->close();
