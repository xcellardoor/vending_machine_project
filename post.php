<?php

include('./includes/credentials.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database, $db_handle);


if (isset($_POST['stockroom_alter_product_submit'])) {
    $new_value = $_POST['new_product_value'];
    $product_name = $_POST['product_list'];
    $column_array = $_POST['column_list'];

    $query = "UPDATE product_table SET $column_array ='$new_value' WHERE product_name='$product_name';";
    print $query;
    #die("test");
    mysql_query($query) or die ("Cannot update database");
    header("location:stockroom.php");

}

if (isset($_POST['add_product_submit'])) {
    #vars
    $new_product_id = $_POST['add_product_name'];
    $new_machine_id = $_POST['add_vending_machine'];
    $new_quantity_in_machine = $_POST['new_quantity'];
    $new_best_before = $_POST['new_best_before'];

    $query = "INSERT INTO vending_table (product_id, machine_id, quantity_in_machine, best_before) VALUES ('$new_product_id', '$new_machine_id', '$new_quantity_in_machine', '$new_best_before');";
    mysql_query($query) or die ("Cannot update database");
    header("location:vending.php");
}

if (isset($_POST['remove_product_submit'])) {
    #vars
    $product_id = $_POST['remove_product_name'];
    $machine_id = $_POST['remove_vending_machine'];
    print $machine_id . " " . $product_id;
    $query = "DELETE FROM vending_table WHERE product_id='$product_id' and machine_id='$machine_id';";
    mysql_query($query) or die ("Cannot update database");
    header("location:vending.php");
}

if (isset($_POST['remove_stockroom_product_submit'])) {
    if (isset($_POST['remove_stockroom_checkbox'])) {
        $remove_product_name = $_POST['remove_product_name'];
        $query = "DELETE FROM product_table WHERE product_name='$remove_product_name';";
        mysql_query($query) or die ("Cannot update database");
        header("location:stockroom.php");
    } else {
        header("location:stockroom.php");
    }
}

if (isset($_POST['alter_product_submit'])) {
    $alter_product_id = $_POST['alter_product_id'];
    $alter_machine_id = $_POST['alter_machine_id'];
    $alter_product_choice = $_POST['alter_product_choice'];
    $alter_product_new_value = $_POST['alter_product_new_value'];
    $query = "UPDATE vending_table set $alter_product_choice = $alter_product_new_value where product_id = $alter_product_id and machine_id=$alter_machine_id;";
    mysql_query($query) or die ("Unable to Alter Product");
    header("location:vending.php");
}

if (isset($_POST['stockroom_new_stock_submit'])) {
    $product_name = $_POST['stockroom_new_product_name'];
    $stock_level = $_POST['stockroom_new_stock_level'];
    $stock_alert = $_POST['stockroom_new_stock_alert'];
    $purchase_price = $_POST['stockroom_new_purchase_price'];
    $sale_price = $_POST['stockroom_new_sale_price'];

    //Find the next available product_id value
    $highest_product_id_query = mysql_query("SELECT MAX( product_id ) AS max FROM `product_table`;");
    $highest_product_id_fetch = mysql_fetch_array($highest_product_id_query);
    $highest_product_id = $highest_product_id_fetch['max'];


    $query = "INSERT INTO product_table (product_name, stock_purchase_price, stock_sale_price, remaining_stock, low_stock_alert) VALUES ('$product_name', '$purchase_price', '$sale_price', '$stock_level', '$stock_alert');";
    print $query;

    mysql_query($query) or die ("Unable to Add Product to Stockroom");
    header("location:stockroom.php");
}

if (isset($_POST['vending_new_machine_submit'])) {
    $machine_id = $_POST['vending_new_machine_id'];
    $building = $_POST['vending_new_machine_building'];
    $floor = $_POST['vending_new_machine_floor'];

    $query = "INSERT INTO machine_table (machine_id, building, floor) VALUES ('$machine_id', '$building', '$floor');";
    mysql_query($query) or die ("Unable to Add Machine to Machine Table");
    header("location:vending.php");

}

if (isset($_POST['vending_remove_machine_submit'])) {
    //*echo '<script>if(window.confirm("Are you SURE you wish to delete the vending machine, and loose record of both it and all the products it currently contains?"));</script>';
    if ($_POST['remove_stockroom_checkbox'] == 'true') {


        $machine_to_delete = $_POST['vending_remove_machine_dropdown'];
        //echo $machine_to_delete;
        $query = "DELETE FROM machine_table WHERE machine_id='$machine_id';";
        mysql_query($query) or die ("Unable to delete Vending Machine");
        header("location:vending.php");
    } else {
        //Do Nothing!! Don't touch anything!!
    }

}

if (isset($_POST['vending_alter_machine_submit'])) {
    $machine_id = $_POST['vending_alter_machine_id'];
    $attribute_to_alter = $_POST['vending_alter_machine_attribute'];
    $new_value = $_POST['vending_alter_machine_value'];

    $query = "UPDATE machine_table set $attribute_to_alter = '$new_value' where machine_id=$machine_id;";
    mysql_query($query) or die ("Unable to Alter Machine");
    header("location:vending.php");

} else {
    die("No value set");
}

mysql_close($db_handle);

?>