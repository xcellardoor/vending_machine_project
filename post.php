<?php

include('credentials.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database,$db_handle);


if(isset($_POST['submit'])){
	$new_value=$_POST['new_product_value'];
	$product_name=$_POST['product_list'];
	$column_array=$_POST['column_list'];

	$query="update product_table set ".$column_array.'=\''.$new_value."' where product_name='$product_name';";
	print $query;
	#die("test");
	mysql_query($query) or die ("Cannot update database");
	header("location:stockroom.php");

}

if(isset($_POST['add_product_submit'])){
	#vars
	$new_product_id=$_POST['add_product_name'];
	$new_machine_id=$_POST['add_vending_machine'];
	$new_quantity_in_machine=$_POST['new_quantity'];
	$new_best_before=$_POST['new_best_before'];

	$query = "INSERT INTO vending_table (product_id, machine_id, quantity_in_machine, best_before) VALUES ('$new_product_id', '$new_machine_id', '$new_quantity_in_machine', '$new_best_before');";
	mysql_query($query) or die ("Cannot update database");
	header("location:vending.php");
}

if(isset($_POST['remove_product_submit'])){
	#vars
	$product_id=$_POST['remove_product_name'];
	$machine_id=$_POST['remove_vending_machine'];
	print $machine_id." ".$product_id;
	$query = "DELETE FROM vending_table WHERE product_id='$product_id' and machine_id='$machine_id';";
	mysql_query($query) or die ("Cannot update database");
	header("location:vending.php");
}

else{
	die("No value set");
}

?>