<?php
if (isset($_REQUEST["financial_sort_by_dropdown"])) {

    $sort_by_instruction = $_REQUEST["financial_sort_by_dropdown"];
} else {
    $sort_by_instruction = "sale_number";
}

include('./includes/credentials.php');
include('./includes/shared_php_functions.php');

//Establish MySQL server connection.
$connection = new mysqli($server, $user_name, $password, $database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

//Compose an initial query to derive the all-time profit out of the database.
$SQL = "SELECT SUM(profit_made) AS Profit FROM sales_table;";

//Execute that query.
$result = $connection->query($SQL);

setlocale(LC_MONETARY, 'en_GB.UTF-8');

//Apply monetary formatting to the value retrieved by the database.
while ($db_field = $result->fetch_assoc()) {
    $all_time_cash = money_format('%n', ($db_field['Profit'] / 100));
}

//Begin composing the reply for the calling parent page,
$reply = "<table id='financial_table' class='tablesorter'><thead><tr><th colspan='4'>All Time Profit: $all_time_cash</th></tr><tr><th>Sale Number</th><th>Product Name</th><th>Sale Date</th><th>Profit</th></tr></thead><tbody>";

//Pull everything out of the sales table.
$SQL = "SELECT * FROM sales_table;";

//Begin using if statements to check for the filter flag that was set when this page was called.
if ($sort_by_instruction == 'popularity') {

    //Fetch appropriate variables.
    $older_date = $_REQUEST['popularity_older_date'];
    $newer_date = $_REQUEST['popularity_newer_date'];

    //Select sales where the sale date falls between the two dates provided by the user.
    $SQL = "SELECT SUM(profit_made) AS profit_between_dates FROM sales_table WHERE date_of_sale>='$older_date' AND date_of_sale<='$newer_date';";
    $profit_between_dates = "";

    $result = $connection->query($SQL);

    //Create a variable to hold the profit between dates, but format it as money, based on our locale.
    while ($db_field = $result->fetch_assoc()) {
        $profit_between_dates .= money_format('%n', ($db_field['profit_between_dates'] / 100));
    }

    //Redefine $reply with new content, since a different path of execution has been chosen through the script.
    $reply = "<table id='financial_table' class='tablesorter'><thead><tr><th colspan='4'>All Time Profit: $all_time_cash</th></tr><tr><th colspan='4'>Profit Between Dates: $profit_between_dates </th></tr><tr><th>Product Name</th><th>Amount Sold</th></tr></thead><tbody>";

    //More complicated SQL query used to extract all sales records from the database which fall between the dates, and are then grouped together by a count of their occurance
    $SQL = "SELECT COUNT(*), sales_table.product_name from sales_table WHERE date_of_sale>='$older_date' AND date_of_sale<='$newer_date'group by sales_table.product_id ORDER BY COUNT(*) DESC;";

    $result = $connection->query($SQL);

    //Fetch results from the query and compound them one by one to the response.
    while ($db_field = $result->fetch_assoc()) {
        $reply .= "<tr><td>" . $db_field['product_name'] . "</td><td>" . $db_field['COUNT(*)'] . "</td></tr>";
    }

} elseif ($sort_by_instruction == 'name') {
    //Fetch needed value from the URL request string.
    $name = $_REQUEST['name_value'];

    //Grab everything from the sales table where the product's name is LIKE that which was entered - so part of a name can be typed and it may still be found
    $SQL = "SELECT * FROM sales_table WHERE sales_table.product_name LIKE '$name%' order by sale_number;";

    $result = $connection->query($SQL);

    //Compose the reply, and include all required database elements.
    while ($db_field = $result->fetch_assoc()) {
        $reply .= "<tr><td>" . $db_field['sale_number'] . "</td><td>" . $db_field['product_name'] . "</td><td>" . $db_field['date_of_sale'] . "</td><td>" . money_format('%n', ($db_field['profit_made'] / 100)) . "</td></tr>";
    }

} elseif ($sort_by_instruction == "between_dates") {
    //Fetch needed data from the URL string
    $older_date = $_REQUEST["between_dates_older_date"];
    $newer_date = $_REQUEST["between_dates_newer_date"];
    //Compose the query which filters sales between certain dates
    $SQL = "SELECT * FROM sales_table WHERE date_of_sale>='$older_date' AND date_of_sale<='$newer_date' order by date_of_sale DESC;";

    $result = $connection->query($SQL);

    //Compose the table, and place retrieved data in the relevant fields.
    while ($db_field = $result->fetch_assoc()) {
        $reply .= "<tr><td>" . $db_field['sale_number'] . "</td><td>" . $db_field['product_name'] . "</td><td>" . $db_field['date_of_sale'] . "</td><td>" . money_format('%n', ($db_field['profit_made'] / 100)) . "</td></tr>";
    }

} else {
    //If no special search parameters were passed when calling this script, this is the 'go-to' reponse.
    $result = $connection->query($SQL);

    //Fetch database attributes to send back.
    while ($db_field = $result->fetch_assoc()) {
        $reply .= "<tr><td>" . $db_field['sale_number'] . "</td><td>" . $db_field['product_name'] . "</td><td>" . $db_field['date_of_sale'] . "</td><td>" . money_format('%n', ($db_field['profit_made'] / 100)) . "</td></tr>";
    }
}

//Close up remaining open tags
$reply .= "</tbody></table>";

//Send the long compose reply string back to the parent page.
echo $reply;

//Close off the database connection.
$connection->close();