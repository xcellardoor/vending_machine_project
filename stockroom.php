<html>

<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="./js/jquery.tablesorter/themes/blue/style.css">
    <script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="./includes/shared_javascript_functions.js"></script>

    <script type="text/javascript" src="./js/jquery.tablesorter/jquery.tablesorter.js"></script>

    <script>

        $(document).ready(function () {
                $("table").tablesorter();
                $('#remove_stockroom_product_submit').prop('disabled', true);
            }
        );


        $(function () {
            var $sidebar = $("#stockroom_amendment_section"),
                $window = $(window),
                offset = $sidebar.offset(),
                topPadding = 15;

            $window.scroll(function () {
                if ($window.scrollTop() > offset.top) {
                    $sidebar.stop().animate({
                        marginTop: $window.scrollTop() - offset.top + topPadding
                    });
                } else {
                    $sidebar.stop().animate({
                        marginTop: 0
                    });
                }
            });

        });

        function filter_selections(argument) {

            switch (argument) {
                case "product_name":
                    var result = "Type start of name and click Filter<br><input id='stockroom_filter_product_name' placeholder='Product Name?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "remaining_stock":
                    var result = "<select id='stockroom_filter_stock_direction'><option value='gt' selected>Greater than or equal to</option><option value='lt'>Less than or equal to</option></select><input id='stockroom_filter_stock_value' placeholder='Amount of Stock?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "low_stock":
                    var result = "Type stock amount and click Filter<br><input id='stockroom_filter_low_stock' placeholder='Low Stock?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                default:
                    document.getElementById('filter_options').innerHTML = "";
            }
        }

        function filter_table() {
            var request = $.ajax({
                url: "stockroom_table_content.php?stockroom_filter_dropdown=" + $('#stockroom_filter_dropdown').val() + "&stockroom_filter_stock_direction=" + $('#stockroom_filter_stock_direction').val() + "&stockroom_filter_stock_value=" + $('#stockroom_filter_stock_value').val() + "&stockroom_filter_product_name=" + $('#stockroom_filter_product_name').val() + "&stockroom_filter_low_stock=" + $('#stockroom_filter_low_stock').val(),
                type: "GET",
                dataType: "html"
            });

            request.done(function (msg) {
                $("#table_section").html(msg);
                $("table").tablesorter();
            });

            request.fail(function (jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });
        }

        function open_order_window() {
            newwindow = window.open('./orders.php', 'name', 'height=600,width=800');
            if (window.focus) {
                newwindow.focus()
            }
        }
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
    <div style="float: left; width: 50%" align="center">

        <select id='stockroom_filter_dropdown' onchange="filter_selections(this.value)">
            <option value="no_filter" selected>No Filter</option>
            <option value="product_name">Product Name</option>
            <option value="in_stock">In Stock</option>
            <option value="out_of_stock">Out Of Stock</option>
            <option value="remaining_stock">Remaining Stock</option>
            <!--<option value="sale_price">Sale Price</option>
            <option value="purchase_price">Purchase Price</option>
            <option value="low_stock">Low Stock Alert</option>-->
        </select>
        <button type="button" onclick="filter_table()">Filter!</button>
        <br>

        <div id="filter_options">

        </div>

        <div id="table_section">
            <?php
            include('./stockroom_table_content.php');

            ?>
        </div>
        <br>

    </div>
    <div style='float: left; width: 50%' align=center id="stockroom_amendment_section">

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
            echo dropdown_menu('column_list', ['product_name', 'stock_purchase_price', 'stock_sale_price', 'remaining_stock', 'low_stock_alert'], ['Product Name', 'Stock Purchase Price', 'Stock Sale Price', 'Remaining Stock', 'Low Stock Alert'], 1);
            ?>

            <input name="new_product_value" size="15" placeholder="New Value"/>
            <input name="stockroom_alter_product_submit" type="submit" value="Update Database"/>

            <!--ADDITION-->
            <br><br>

            <h3>Add Product</h3>
            <table>
                <tr>
                    <th>Product Name
                    <th>Initial Stock
                    <th>Low Stock Alert</th>
                </tr>
                <tr>
                    <td><input name='stockroom_new_product_name' style='width:100%' placeholder="New Product Name"></td>
                    <td><input name='stockroom_new_stock_level' style='width:100%' placeholder="Initial Stock Level">
                    </td>
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

            <br>

            <h3>Remove Product</h3>
            <table>
                <tr>
                    <th>Product Name</th>
                </tr>
                <tr>
                    <td> <?php echo dropdown_menu('remove_product_name', $product_names, $product_names, 0); ?></td>
                    <td>Confirm Delete?<input type="checkbox" id="remove_stockroom_checkbox"
                                              onclick="toggle_button('remove_stockroom_product_submit')"
                                              id="remove_stockroom_checkbox" value="true"></td>
                    <td><input id="remove_stockroom_product_submit" type="submit" value="Remove Product"/></td>
                </tr>
            </table>

            <button id='order_window_button' type="button" onclick="open_order_window()">Orders</button>

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
