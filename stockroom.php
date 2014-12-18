<html>

<head>
<link rel="stylesheet" type="text/css" href="stylesheet.css"
</head>
<title>Stockroom</title>

<body id="main-body">
<?php


function dropdown_menu($name, array $options, $selected=null){
	$dropdown = '<select name="'.$name.'" id="'.$name.'">'."\n";
	$selected = $selected;

	foreach($options as $key=>$option){
		$select = $selected==$key ? ' selected' : null;

		$dropdown .= '<option value="'.$option.'"'.$select.'>'.$option.'</option>'."\n";

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
	print "<tr><th>Product Name<th>Remaining Stock<th>Low Stock Alert<th>Stock State</th></tr>"; 
	while ($db_field = mysql_fetch_assoc($result)){
		print "<tr><td>".$db_field['product_name']."<td>".$db_field['remaining_stock']."<td>".$db_field['low_stock_alert'];

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
print "<div style='float: left; width: 50%'>";

print "<form name=stock_update' method='post' action='post.php'>";
$name = 'product_list';
$SQL = "SELECT * FROM product_table";
$result = mysql_query($SQL);

$product_array = array();
$column_array = array();

while($db_field = mysql_fetch_assoc($result)){
	array_push($product_array, $db_field['product_name']);
}

$selected = 0;

echo dropdown_menu($name, $product_array, $selected);

$name = 'column_list';
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
echo dropdown_menu($name, $column_array, $selected);

print "New Value: <input name=\"new_product_value\" size=\"15\"/> 	";
print "<input name=\"submit\" type=\"submit\" value=\"Update Database\" />";

print "</form>";

print "</div>";

mysql_close($db_handle);

?>

<?php
include('footer.php');

?>
</body>

</html>
