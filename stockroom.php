<html>

<head>
<link rel="stylesheet" type="text/css" href="stylesheet.css"
</head>
<title>Stockroom</title>

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

include "menu.php";

print '<br>';
print '<div style="float: left; width: 50%">';



print "<table class='center'>";
include('credentials.php');

$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database,$db_handle);


#$conn = new mysqli($server, $user_name,)

if($db_found){
	$SQL = "SELECT * FROM product_table";
	$result = mysql_query($SQL);
	print "<tr><th>Product Name<th>Remaining Stock<th>Purchase Price<th>Sale price<th>Low Stock Alert<th>Stock State</th></tr>";
	while ($db_field = mysql_fetch_assoc($result)){
		print "<tr><td>".$db_field['product_name']."<td>".$db_field['remaining_stock']."<td>".$db_field['stock_purchase_price']."<td>".$db_field['stock_sale_price']."<td>".$db_field['low_stock_alert'];

		if($db_field['low_stock_alert']>$db_field['remaining_stock']){
			print "<td BGCOLOR=\"#EE0000\">LOW!</td></tr>";
		}
		else{
			print "<td BGCOLOR=\"#00ff00\">OK</td></tr>";
		}
	}
	//MYSQL CLOSE could go here
}

else {
	print "Database Access Error!";
	mysql_close($db_handle);

}

print "</table><br>";

print "</div>";
print "<div style='float: left; width: 50%' align=center>";

print "<form name=stock_update' method='post' action='post.php'>";

$SQL = "SELECT * FROM product_table";
$result = mysql_query($SQL);

$product_array = array();
$column_array = array();

while($db_field = mysql_fetch_assoc($result)){
	array_push($product_array, $db_field['product_name']);
}

$selected = 0;

$SQL = 'SELECT `COLUMN_NAME`
FROM `INFORMATION_SCHEMA`.`COLUMNS` 
WHERE `TABLE_SCHEMA`=\'vending_database\' 
    AND `TABLE_NAME`=\'product_table\';';
$result = mysql_query($SQL);
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	array_push($column_array, $row['COLUMN_NAME']);
	#print_r($row);
	#$column_array[] = $row;

}

print "<h2>Alter Existing</h2>";

echo dropdown_menu('product_list', $product_array, $product_array, $selected);

echo dropdown_menu('column_list', $column_array, $column_array, $selected);

print "New Value: <input name=\"new_product_value\" size=\"15\"/> 	";
print "<input name=\"stockroom_alter_product_submit\" type=\"submit\" value=\"Update Database\" />";

//ADDITION
print "<h2>Addition</h2>";
print "<table><tr><th>Product Name<th>Initial Stock<th>Low Stock Alert</th></tr>";
print "<td><input name='stockroom_new_product_name' style=\'width:100%\' placeholder=\"New Product Name\" >";
print "<td><input name='stockroom_new_stock_level' style=\'width:100%\' placeholder=\"Initial Stock Level\">";
print "<td><input name='stockroom_new_stock_alert' style=\'width:100%\' placeholder=\"Low Stock Alert\"></tr>";
print "<tr><th>Stock Purchase Price (Pence)<th>Stock Sale Price (Pence)</th></tr>";
print "<tr><td><input name='stockroom_new_purchase_price' style=\'width:100%\' placeholder=\"Purchase Price\">";
print "<td><input name='stockroom_new_sale_price' style=\'width:100%\' placeholder=\"New Sale Price\">";
print "<td><input name=\"stockroom_new_stock_submit\" type=\"submit\" value=\"Add Product\" /></td></tr>";

print "</table>";


$product_names = array();

$SQL = 'select * from product_table;';
$result = mysql_query($SQL);

while($db_field = mysql_fetch_assoc($result)) {
	array_push($product_names, $db_field['product_name']);
}


print "<h2>Removal</h2>";
print "<table>";
print "<tr><th>Product</th></tr>";
print "<tr><td>"; echo dropdown_menu('remove_product_name', $product_names, $product_names, 0); print "</td>";
print "<td><input name=\"remove_stockroom_product_submit\" type=\"submit\" value=\"Remove Product\" /></td></tr>";
print "</table>";

print "<h2>Quick Stock Amendment</h2>";

print "</form>";

print "</div>";

mysql_close($db_handle);

?>

<?php
include('footer.php');

?>
</body>

</html>
