<?php
echo '<div align=center>';

//This IF ELSE allows this page to be included and a default value of machine_id to be used for sorting if no other is passed
if (isset($_REQUEST["order_number"])) {
    $order_number = $_REQUEST["order_number"];
} else {
    echo "Sorry, that doesn't appear to be a valid Order ID";
    echo "<button type='button'>Close Window</button>";
}

include('./includes/credentials.php');

$connection = new mysqli($server, $user_name, $password, $database);

date_default_timezone_set('Europe/London');

if ($connection->connect_error) {
    die("Connection failed with: " . $connection->connect_error);
}

    $SQL = "SELECT * from order_details WHERE order_number=$order_number;";

    $result = $connection->query($SQL);

if($result->num_rows>0) {


    echo "<table id='table' class='tablesorter'><thead><tr><th>Order Details for Order Number $order_number</th></tr><tr><th>Product</th><th>Quantity</th></tr></thead><tbody>";

    while($db_field = $result->fetch_assoc()){
        echo "<tr><td>" . $db_field['product_name'] . "</td><td>" . $db_field['quantity'] . "</td></tr>";
    }

    echo "</tbody></table>";

    echo "<button type='button' onclick='window.close()'>Close Window</button>";
}

else{
    echo "0 results";
}
    echo '</div>';

$connection->close();
