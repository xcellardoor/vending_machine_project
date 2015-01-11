<?php

include('./includes/credentials.php');

//$db_handle = mysql_connect($server, $user_name, $password);
//$db_found = mysql_select_db($database, $db_handle);

$connection = new mysqli($server, $user_name, $password, $database);
if ($connection->connect_error){
    die("Connection failed: " . $connection->connect_error);
}

if (isset($_POST['stockroom_alter_product_submit'])) {
    $new_value = $_POST['new_product_value'];
    $product_name = $_POST['product_list'];
    $column_array = $_POST['column_list'];
    $SQL = "UPDATE product_table SET $column_array ='$new_value' WHERE product_name='$product_name';";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }
    header("location:stockroom.php");
    $connection->close();
}

if (isset($_POST['add_product_submit'])) {
    #vars
    $new_product_id = $_POST['add_product_name'];
    $new_machine_id = $_POST['add_vending_machine'];
    $new_quantity_in_machine = $_POST['new_quantity'];
    $new_best_before = $_POST['new_best_before'];

    $SQL = "INSERT INTO vending_table (product_id, machine_id, quantity_in_machine, best_before) VALUES ('$new_product_id', '$new_machine_id', '$new_quantity_in_machine', '$new_best_before');";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        die
        ("Cannot update record: " . $connection->error);
        #header("vending.php?alert='Cannot update record! " . $connection->connect_error);
    }

    $SQL = "UPDATE product_table set remaining_stock = remaining_stock-'$new_quantity_in_machine' where product_id='$new_product_id';";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }
    header("location:vending.php");
    $connection->close();
}

if (isset($_POST['remove_product_submit'])) {
    $product_id = $_POST['remove_product_name'];
    $machine_id = $_POST['remove_vending_machine'];
    $quantity_left_in_machine=0; //So that we don't accidentally add or remove stock if commands below fail


    $SQL = "SELECT * from vending_table WHERE vending_table.machine_id='$machine_id' and vending_table.product_id='$product_id';";

    $result=$connection->query($SQL);
    if ($result == TRUE){
        while($db_field = $result->fetch_assoc())
        $quantity_left_in_machine = $db_field['quantity_in_machine'];
    }
    else{
        echo "Cannot update record: " . $connection->error;
    }

    $SQL = "UPDATE product_table set remaining_stock = remaining_stock+$quantity_left_in_machine;";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }

    $SQL = "DELETE FROM vending_table WHERE product_id='$product_id' and machine_id='$machine_id';";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }

    header("location:vending.php");
    $connection->close();
}

if (isset($_POST['remove_stockroom_product_submit'])) {
    $remove_product_name = $_POST['remove_product_name'];
    $SQL = "DELETE FROM product_table WHERE product_name='$remove_product_name';";
    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }
    header("location:stockroom.php");
    $connection->close();;
}

if (isset($_POST['alter_product_submit'])) {
    $alter_product_id = $_POST['alter_product_id'];
    $alter_machine_id = $_POST['alter_machine_id'];
    $alter_product_choice = $_POST['alter_product_choice'];
    $alter_product_new_value = $_POST['alter_product_new_value'];
    $SQL = "UPDATE vending_table set $alter_product_choice = $alter_product_new_value where product_id = $alter_product_id and machine_id=$alter_machine_id;";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }
    header("location:vending.php");
    $connection->close();
}

if (isset($_POST['stockroom_new_stock_submit'])) {
    $product_name = $_POST['stockroom_new_product_name'];
    $stock_level = $_POST['stockroom_new_stock_level'];
    $stock_alert = $_POST['stockroom_new_stock_alert'];
    $purchase_price = $_POST['stockroom_new_purchase_price'];
    $sale_price = $_POST['stockroom_new_sale_price'];

    $SQL = "INSERT INTO product_table (product_name, stock_purchase_price, stock_sale_price, remaining_stock, low_stock_alert) VALUES ('$product_name', '$purchase_price', '$sale_price', '$stock_level', '$stock_alert');";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }
    header("location:stockroom.php");
    $connection->close();
}

if (isset($_POST['vending_new_machine_submit'])) {
    $machine_id = $_POST['vending_new_machine_id'];
    $building = $_POST['vending_new_machine_building'];
    $floor = $_POST['vending_new_machine_floor'];

    $SQL = "INSERT INTO machine_table (machine_id, building, floor) VALUES ('$machine_id', '$building', '$floor');";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }
    header("location:vending.php");
    $connection->close();

}

if (isset($_POST['vending_remove_machine_submit'])) {
    $machine_to_delete = $_POST['vending_remove_machine_dropdown'];
    $SQL = "DELETE FROM machine_table WHERE machine_id='$machine_to_delete';";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }
    header("location:vending.php");
    $connection->close();
}

if (isset($_POST['vending_alter_machine_submit'])) {
    $machine_id = $_POST['vending_alter_machine_id'];
    $attribute_to_alter = $_POST['vending_alter_machine_attribute'];
    $new_value = $_POST['vending_alter_machine_value'];

    $SQL = "UPDATE machine_table set $attribute_to_alter = '$new_value' where machine_id=$machine_id;";

    if ($connection->query($SQL) == TRUE){

    }
    else{
        echo "Cannot update record: " . $connection->error;
    }
    header("location:vending.php");
    $connection->close();

} else {
    $connection->close();
    die("No value set");
}