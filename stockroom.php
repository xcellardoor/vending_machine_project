<html>

<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="./js/jquery.tablesorter/themes/blue/style.css">
    <script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>

    <script type="text/javascript" src="./js/jquery.tablesorter/jquery.tablesorter.js"></script>

    <script>

        $(document).ready(function()
            {
                $("table").tablesorter();
            }
        );
    </script>

</head>
<title>Stockroom | Vending Machine Management System</title>

<body>


<?php

/**function dropdown_menu($name, array $values, array $options, $selected=null){
 * $dropdown = '<select name="'.$name.'" id="'.$name.'">'."\n";
 * $selected = $selected;
 *
 * #foreach($options as $key=>$option){
 * foreach (array_combine($values, $options) as $id=>$value){
 *
 * $select = $selected==$value ? ' selected' : null;
 *
 * $dropdown .= '<option value="'.$id.'"'.$select.'>'.$value.'</option>'."\n";
 *
 * }
 * $dropdown .= '</select>'."\n";
 *
 * return $dropdown;
 * }**/

include "./includes/menu.php";
include "./includes/shared_php_functions.php";

?>

<div id="main-body">
    <div align="center"><h1>Stockroom Management</h1></div>
    <div style="float: left; width: 50%">

        <table class='tablesorter' id="stockroom_table">
            <?php
            include('./includes/credentials.php');

            $db_handle = mysql_connect($server, $user_name, $password);
            $db_found = mysql_select_db($database, $db_handle);


            #$conn = new mysqli($server, $user_name,)

            if ($db_found) {
                $SQL = "SELECT * FROM product_table";
                $result = mysql_query($SQL);
                print "<thead><tr><th>Product Name<th>Remaining Stock<th>Purchase Price<th>Sale price<th>Low Stock Alert<th>Stock State</th></tr></thead><tbody>";
                while ($db_field = mysql_fetch_assoc($result)) {
                    print "<tr><td>" . $db_field['product_name'] . "<td>" . $db_field['remaining_stock'] . "<td>" . $db_field['stock_purchase_price'] . "<td>" . $db_field['stock_sale_price'] . "<td>" . $db_field['low_stock_alert'];

                    if ($db_field['low_stock_alert'] > $db_field['remaining_stock']) {
                        print "<td BGCOLOR=\"#EE0000\">LOW!</td></tr>";
                    } else {
                        print "<td BGCOLOR=\"#26D82F\">OK</td></tr>";
                    }
                }
                //MYSQL CLOSE could go here
            } else {
                print "Database Access Error!";
                mysql_close($db_handle);

            }
            ?>
        </tbody>
        </table>
        <br>

    </div>
    <div style='float: left; width: 50%' align=center>

        <form name=stock_update' method='post' action='post.php'>

            <?php
            $SQL = "SELECT * FROM product_table";
            $result = mysql_query($SQL);

            $product_array = array();
            $column_array = array();

            while ($db_field = mysql_fetch_assoc($result)) {
                array_push($product_array, $db_field['product_name']);
            }

            $selected = 0;

            $SQL = 'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="vending_database" AND `TABLE_NAME`="product_table";';
            $result = mysql_query($SQL);
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                array_push($column_array, $row['COLUMN_NAME']);

            }
            ?>
            <h3>Alter Existing Product</h3>

            <?php
            echo dropdown_menu('product_list', $product_array, $product_array, 1);
            //echo dropdown_menu('column_list', $column_array, $column_array, 1);
            echo dropdown_menu('column_list', ['product_name','stock_purchase_price','stock_sale_price', 'remaining_stock', 'low_stock_alert'], ['Product Name', 'Stock Purchase Price', 'Stock Sale Price', 'Remaining Stock', 'Low Stock Alert'], 1);
            ?>

            <input name="new_product_value" size="15" placeholder="New Value"/>
            <input name="stockroom_alter_product_submit" type="submit" value="Update Database"/>

            <!--ADDITION-->
            <br><br><h3>Add Product</h3>
            <table>
                <tr>
                    <th>Product Name
                    <th>Initial Stock
                    <th>Low Stock Alert</th>
                </tr>
                <tr>
                <td><input name='stockroom_new_product_name' style='width:100%' placeholder="New Product Name"></td>
                <td><input name='stockroom_new_stock_level' style='width:100%' placeholder="Initial Stock Level"></td>
                <td><input name='stockroom_new_stock_alert' style='width:100%' placeholder="Low Stock Alert"></td>
                </tr>
                <tr>
                    <th>Stock Purchase Price (Pence)
                    <th>Stock Sale Price (Pence)</th>
                </tr>
                <tr>
                    <td><input name='stockroom_new_purchase_price' style='width:100%' placeholder="Purchase Price">
                    <td><input name='stockroom_new_sale_price' style='width:100%' placeholder="New Sale Price"></td>
                    <td><input name="stockroom_new_stock_submit" type="submit" value="Add Product"/></td>
                </tr>

            </table>

            <?php
            $product_names = array();

            $SQL = 'select * from product_table;';
            $result = mysql_query($SQL);

            while ($db_field = mysql_fetch_assoc($result)) {
                array_push($product_names, $db_field['product_name']);
            }
            ?>

            <br><h3>Remove Product</h3>
            <table>
                <tr>
                    <th>Product Name</th>
                </tr>
                <tr>
                    <td> <?php echo dropdown_menu('remove_product_name', $product_names, $product_names, 0); ?></td>
                    <td><input name="remove_stockroom_product_submit" type="submit" value="Remove Product" /></td>
                </tr>
            </table>

            <!--<h3>Quick Stock Amendment</h3>-->

        </form>

    </div>

    <div class="clear"></div>
</div>
<?php
include('./includes/footer.php');
mysql_close($db_handle);
?>

</body>

</html>
