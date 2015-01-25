<?php

//This IF ELSE allows this page to be included and a default value of machine_id to be used for sorting if no other is passed
if (isset($_REQUEST["stockroom_sort_by_dropdown"])) {
    $order_instruction = $_REQUEST["stockroom_sort_by_dropdown"];
} else {
    $order_instruction = "machine_id";
}

//This IF ELSE allows this page to be included and a default value of machine_id to be used for sorting if no other is passed
if (isset($_REQUEST['stockroom_filter_dropdown'])) {
    $stockroom_filter_dropdown = $_REQUEST['stockroom_filter_dropdown'];
} else {
    $stockroom_filter_dropdown = "unassigned";
}

include('./includes/credentials.php');

//Initialise database connection
$connection = new mysqli($server, $user_name, $password, $database);


date_default_timezone_set('Europe/London');
//Check for connection error and die if there is one.
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

//Use non-prepared SQL statement to find product names and order them - okay to use non-prepared since no user-input is involed.
$SQL = "SELECT * FROM product_table ORDER BY product_name ASC;";

//First optional execution path through this script - Override rules to get the table to show what we actually want.
if ($stockroom_filter_dropdown == "remaining_stock") {
    $stock_value = $_REQUEST['stockroom_filter_stock_value'];
    $direction = $_REQUEST['stockroom_filter_stock_direction'];

    //Reassign $direction to something that SQL can use based upon what the user entered
    if ($direction == "lt") {
        $direction = "<=";
    } else {
        $direction = ">=";
    }

    //Another query, used to find products with a certain remaining stock level.
    $SQL = "SELECT * from product_table WHERE remaining_stock$direction$stock_value ORDER BY product_name ASC;";

}

//Depending on the user's choice, choose an SQL statement
if ($stockroom_filter_dropdown == "product_name") {
    $product_name = $_REQUEST['stockroom_filter_product_name'];
    //Find products which have a name like the one the user entered.
    $SQL = "SELECT * from product_table WHERE product_name LIKE '$product_name%';";
}

if ($stockroom_filter_dropdown == "in_stock") {
    $SQL = "SELECT * from product_table WHERE remaining_stock>low_stock_alert ORDER BY product_name ASC;";
}
if ($stockroom_filter_dropdown == "out_of_stock") {
    $SQL = "SELECT * from product_table WHERE remaining_stock<=low_stock_alert ORDER BY product_name ASC;";
}
if ($stockroom_filter_dropdown == "low_stock") {
    $low_stock = $_REQUEST['stockroom_filter_low_stock'];
    $SQL = "SELECT * from product_table WHERE low_stock LIKE '$low_stock%' ORDER BY product_name ASC;";
}

//Initialise the reply to the HTML parent page.
$reply = "";

//Execute the SQL which has been chosen by the user's actions
$result = $connection->query($SQL);

//Begin constructing the reply to the calling HTML page.
$reply .= "<table class='tablesorter' id='stockroom_table'><thead><tr><th>Product Name<th>Remaining Stock<th>W/S Purchase Price<th>Sale price<th>Low Stock Alert<th>Stock State</th></tr></thead><tbody>";
//Fetch all required elements from the SQL query.
while ($db_field = $result->fetch_assoc()) {
    $reply .= "<tr><td>" . $db_field['product_name'] . "<td>" . $db_field['remaining_stock'] . "<td>£" . money_format('%n', $db_field['stock_purchase_price'] / 100) . "<td>£" . money_format('%n', $db_field['stock_sale_price'] / 100) . "<td>" . $db_field['low_stock_alert'];

    //Perform a check to see if stock is below expectations and if it is, assign an appropriate color to it.
    if ($db_field['low_stock_alert'] > $db_field['remaining_stock']) {
        $reply .= "<td BGCOLOR=\"#FF6666\">LOW!</td></tr>";
    } else {
        $reply .= "<td BGCOLOR=\"#70DB70\">OK</td></tr>";
    }
}
//Close up element tags.
$reply .= "</tbody></table>";

//If the response length is too small then there must not be any results, therefore we can respond saying so.
if (strlen($reply) > 15) {
    echo $reply;
} else {
    echo "<h2>No Results</h2>";
}