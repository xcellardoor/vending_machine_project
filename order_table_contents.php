<?php
//This IF ELSE allows this page to be included and a default value of machine_id to be used for sorting if no other is passed
if (isset($_REQUEST["vending_sort_by_dropdown"])) {
    $order_instruction = $_REQUEST["vending_sort_by_dropdown"];
} else {
    $order_instruction = "machine_id";
}

//Check the request string for values that alter the flow of the script.
if (isset($_REQUEST['vending_filter_dropdown'])) {
    $vending_filter_dropdown = $_REQUEST['vending_filter_dropdown'];
} else {
    $vending_filter_dropdown = "unassigned";
}

//As usual, bring in credentials to use.
include('./includes/credentials.php');

//Establish mysql server connection.
$connection = new mysqli($server, $user_name, $password, $database);
if ($connection->connect_error) {
    die ("Connection failed: " . $connection->connect_error);
}

//Check for the instruction of execution flow in the URL string.
if ($_REQUEST['create_from_low_stock'] = '1') {
    $SQL = "SELECT * FROM product_table WHERE remaining_stock<=low_stock_alert;";
}

date_default_timezone_set('Europe/London');

$result = $connection->query($SQL);

//Compose the table in the reply.
$reply = "<table id='table' class='tablesorter'>";
$reply .= "<thead><tr><th>Product Name</th></tr></thead><tbody><tr><td BGCOLOR='#FF6666'>LOW STOCK</td></tr>";

while ($db_field = $result->fetch_assoc()) {
    $reply .= "<tr><td>" . $db_field['product_name'] . "</tr>";

}
//Finish off the reply by adding close tags
$reply .= "</tbody></table>";

//If the length of the reply is greater than 15 then there must be content to send back, so send it back. If not, return 'no results'.
if (strlen($reply) > 15) {
    echo $reply;
} else {
    echo "<h2>No Results</h2>";
}

//Tidy up.
$connection->close();
