<html>

<head>
	<link rel="stylesheet" type="text/css" href="stylesheet.css">
	<script src="jquery-1.7.2.min.js"></script>
	<script>

	</script>
</head>
<title>Vending Machines</title>

<body id="main-body">

<?php

function dropdown_menu($name, array $values, array $options, $selected=null){
	$dropdown = '<select name="'.$name.'" id="'.$name.'">'."\n";
	$selected = $selected;

	#foreach($options as $key=>$option){
	foreach (array_combine($values, $options) as $id=>$value){

		$select = $selected==$value ? ' selected' : null;

		$dropdown .= '<option value="'.$id.'"'.$select.'>'.$value.'</option>'."\n";

	}
	$dropdown .= '</select>'."\n";

	return $dropdown;
}

include('menu.php');

print "<div align=center><h1>Vending Machine Management</h1></div>";

print '<div id="demo" style="float: left; width: 50%">';

print "<table class='center'>";
include('credentials.php');
$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database,$db_handle);

date_default_timezone_set('Europe/London');

if($db_found){
	$SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id order by machine_id;";
	$result = mysql_query($SQL);
	print "<tr><th>Machine<th>Product<th>Quantity<th>Best Before</th></tr>"; 
	while ($db_field = mysql_fetch_assoc($result)){
		print "<tr><td>".$db_field['machine_id']."<td>".$db_field['product_name']."<td>".$db_field['quantity_in_machine'];

		if(strtotime($db_field['best_before'])<strtotime('now')){
			print "<td BGCOLOR=\"#EE0000\">OUT OF DATE!</td></tr>";
		}
		else{
			print "<td BGCOLOR=\"#00ff00\">IN DATE</td></tr>";
		}
	}
}

else {
	print "Database Access Error!";
	mysql_close($db_handle);

}
print "</table>";
print "</div>";
print '<div style="float: left; width: 50%">';

$active_machine_array=array();
$product_array_items=array();
$machines_in_use_array=array();
$product_array_values=array();
$product_table_options=array();
$product_table_values=array();
$vending_table_columns=array();

print "<form name='alter_vending_table' method='post' action='post.php'>";


#$SQL = "SELECT * FROM vending_table";
$SQL = 'select * from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id;';
$result = mysql_query($SQL);

while($db_field = mysql_fetch_assoc($result)) {
	array_push($machines_in_use_array, $db_field['machine_id']);
}
$result = mysql_query($SQL);
while($db_field = mysql_fetch_assoc($result)){
	//array_push($product_array, $db_field['product_id']);
	array_push($product_array_items, $db_field['product_name']);
}

$result=mysql_query($SQL);
while($db_field = mysql_fetch_assoc($result)) {
	array_push($product_array_values, $db_field['product_id']);
}

$SQL = "SELECT machine_id FROM machine_table";
$result = mysql_query($SQL);
while($db_field = mysql_fetch_assoc($result)){
	array_push($active_machine_array, $db_field['machine_id']);
}

$SQL = "SELECT * from product_table;"; $result = mysql_query($SQL);
while($db_field = mysql_fetch_assoc($result)){
	array_push($product_table_values, $db_field['product_id']);
	array_push($product_table_options, $db_field['product_name']);
}

$SQL = 'SELECT `COLUMN_NAME`
FROM `INFORMATION_SCHEMA`.`COLUMNS`
WHERE `TABLE_SCHEMA`=\'vending_database\'
    AND `TABLE_NAME`=\'vending_table\';';
$result = mysql_query($SQL);
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	array_push($vending_table_columns, $row['COLUMN_NAME']);
}

#$product_array_items = array_unique($product_array_items);
$active_machine_array = array_unique($active_machine_array);
$machines_in_use_array = array_unique($machines_in_use_array);
sort($machines_in_use_array);
#sort($product_array_items);

print "<div align=center><h3>Alter Product</h3>";
print "<table cellspacing='0' cellpadding='0'><tr><th>Product Name<th>Vending Machine<th>Attribute to Alter<th>New Value</th></tr>";
#Since the same list of 'existing products in machines' is needed, we can borrow the line used for deleting
print "<tr><td>"; echo dropdown_menu('alter_product_id',$product_array_values, $product_array_items, 0); print "<td>"; echo dropdown_menu('alter_machine_id',$machines_in_use_array, $machines_in_use_array, 0); print "<td>"; echo dropdown_menu('alter_product_choice', $vending_table_columns, $vending_table_columns,0);

print "<td><input name=\"alter_product_new_value\" style=\"width:100%\" placeholder=\"New Value\"/></td>";
print "<td><input name=\"alter_product_submit\" type=\"submit\" value=\"Alter Product\" /></td></tr>";
print "</table>";

print "<h3>Add Product</h3>";

print "<table cellspacing='0' cellpadding='0'>"; 
print "<tr><th>Product Name<th>Machine ID<th>Quantity<th>Best-Before</th></tr>";
print "<tr><td>"; echo dropdown_menu('add_product_name', $product_table_values, $product_table_options, 0); print "</td> 	";
#print "<td><input name=\"new_machine_id\" style=\"width:100%\" placeholder=\"Machine ID\"/></td> 	";
print "<td>"; echo dropdown_menu('add_vending_machine', $active_machine_array, $active_machine_array, 0); print "</td>";
print "<td><input name=\"new_quantity\" style=\"width:100%\" placeholder=\"Quantity\"/></td> 	";
print "<td><input name=\"new_best_before\" style=\"width:100%\" placeholder='2015-01-01' type='date' value='2015-01-01'/></td> 	";
print "<td><input name=\"add_product_submit\" type=\"submit\" value=\"Add Product\" /></td></tr>";
print "</table>";

print "<h3>Remove Product</h3>";
print "<table cellspacing='0' cellpadding='0'>";
print "<tr><th>Product Name<th>Vending Machine</th></tr>";
print "<tr><td>";
echo dropdown_menu('remove_product_name', $product_array_values, $product_array_items, 0);
print "<td>";
echo dropdown_menu('remove_vending_machine', $machines_in_use_array, $machines_in_use_array, 0);
print "<td>";
print "<td><input name=\"remove_product_submit\" type=\"submit\" value=\"Remove Product\" /></td></tr>";

print "</table>";


print "<h3>Add Machine</h3>";
print "<table cellspacing='0' cellpadding='0'>";
print "<tr><th>New Machine ID<th>Building<th>Floor (Optional)</th>";
print "<tr><td><input id='vending_new_machine_id' name=\"vending_new_machine_id\" style=\"width:100%\" placeholder=\"Machine ID\"/>";
print "<td><input name=\"vending_new_machine_building\" style=\"width:100%\" placeholder=\"Building\"/>";
print "<td><input name=\"vending_new_machine_floor\" style=\"width:100%\" placeholder=\"Floor\"/>";
print "<td><input name=\"vending_new_machine_submit\" type=\"submit\" value=\"Add Machine\" /></td></tr>";
print "</table>";


print "<h3>Remove Machine</h3>";
print "</form>";

print "</div>";
print "</div>";
include('footer.php');
mysql_close($db_handle);
?>

</body>
</html>